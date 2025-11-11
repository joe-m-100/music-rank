<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Track;
use App\Services\SpotifyClient;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PHPUnit\Event\Telemetry\Duration;

class ReviewController extends Controller
{
    public function __construct(
        protected SpotifyClient $client
    )
    {
    }

    private function calculateCoreStatistics($tracks, $mean)
    {
        // Calculate duration of album
        $duration = $tracks->sum('duration');

        $hours = (int) ($duration / 3600);
        $mins = (int) (($duration / 60) % 60);

        $album_duration = $hours ? $hours . ' hrs ' . $mins . ' mins' : $mins . ' mins';

        $range = $tracks->max('rating') - $tracks->min('rating');

        $x = $tracks->select('rating')
            ->flatten()
            ->map(function ($value) use ($mean) {
                return pow(($value - $mean), 2);
            })
            ->all();

        $std_dev = sqrt(array_sum($x) / $tracks->count());

        return [
            [
                'name' => 'Overall Rating',
                'value' => $mean
            ],
            [
                'name' => 'Total Duration',
                'value' => $album_duration
            ],
            [
                'name' => 'Range',
                'value' => $range
            ],
            [
                'name' => 'Standard Deviation',
                'value' => round($std_dev, 2)
            ],
            [
                'name' => 'Controversy Index',
                'value' => round($range / $mean, 2)
            ],
        ];
    }

    private function createLineChartData(Collection $tracks, $mean)
    {
        // Line Chart data
        $line_chart_ratings = $tracks->map(function ($track, $n) {
            return [
                'x' => $n + 1,
                'y' => $track['rating'],
            ];
        });

        $line_chart_mean = $tracks->map(function ($track, $n) use ($mean) {
            return [
                'x' => $n + 1,
                'y' => $mean,
            ];
        });

        $current_sum = 0;
        $line_chart_sentiment = $tracks->map(function ($track, $n) use (&$current_sum) {
            $current_sum += $track['rating'];

            return [
                'x' => $n + 1,
                'y' => round(($current_sum) / ($n + 1), 2),
            ];
        });

        return [
            'ratings' => $line_chart_ratings,
            'mean' => $line_chart_mean,
            'sentiment' => $line_chart_sentiment,
        ];
    }

    private function createBarChartData($tracks)
    {
        $bar_chart_data = [];

        for ($n = 0; $n < 10; $n++) {
            $bar_chart_data[] = [
                'category' => ( $n + 1 ),
                'value' => $tracks->where('rating', ($n+1))->count(),
            ];
        }

        return $bar_chart_data;
    }

    private function calculateArtistStatistics($artist_id)
    {
        $artist = Artist::query()->whereId($artist_id)->first();
        return [
            'name' => $artist->name,
            'image' => $artist->image,
            'stats' => [
                [
                    'name' => 'Album/EP Reviews',
                    'value' => Album::query()->whereArtistId($artist_id)->count(),
                ],
                [
                    'name' => 'Average Score',
                    'value' => round(Album::query()
                        ->whereArtistId($artist_id)
                        ->withAvg('tracks', 'rating')
                        ->get()
                        ->avg('tracks_avg_rating'), 2),
                ],
                [
                    'name' => 'Magnum Opus',
                    'value' => Album::query()
                        ->whereArtistId($artist_id)
                        ->withAvg('tracks', 'rating')
                        ->orderByDesc('tracks_avg_rating')
                        ->first()->title,
                ],
                [
                    'name' => 'Perfect Tracks',
                    'value' => Track::whereRating(10)->whereHas('album.artist', function ($query) use ($artist_id) {
                        $query->where('id', $artist_id);
                    })->count()
                ],
                [
                    'name' => 'Terrible Tracks',
                    'value' => Track::whereRating(1)->whereHas('album.artist', function ($query) use ($artist_id) {
                        $query->where('id', $artist_id);
                    })->count()
                ],
                [
                    'name' => 'Most Common Rating',
                    'value' => DB::table('tracks')
                        ->join('albums', 'tracks.album_id', '=', 'albums.id')
                        ->where('albums.artist_id', $artist_id)
                        ->select('tracks.rating', DB::raw('COUNT(*) as count'))
                        ->groupBy('tracks.rating')
                        ->orderByDesc('count')->first()->rating
                ],
            ]
        ];
    }

    public function globals()
    {

        $stats = [
            [
                'title' => 'Albums Reviewed',
                'value' => Album::whereType('Album')->get()->count()
            ],
            [
                'title' => 'Extended Plays Reviewed',
                'value' => Album::whereType('EP')->get()->count()
            ],
            [
                'title' => 'Total Tracks Reviewed',
                'value' => Track::get()->count()
            ],
            [
                'title' => 'Average Track Rating',
                'value' => round(Track::get()->avg('rating'), 2)
            ],
            [
                'title' => 'Average Album Rating',
                'value' => round(Album::withAvg('tracks', 'rating')->get()->avg('tracks_avg_rating'), 2)
            ],
            [
                'title' => 'Highest Rated Album',
                'value' => Album::query()->withAvg('tracks', 'rating')->orderByDesc('tracks_avg_rating')->first()->title
            ],
            [
                'title' => 'Perfect Tracks',
                'value' => Track::whereRating(10)->count()
            ],

        ];

        return view('reviews.global-statistics', [
            'stats' => $stats,
            'heading' => 'Global Statistics'
        ]);
    }

    public function review(Request $request)
    {
        return view('review-album', [
            'album' => $this->client->getAlbum($request->query('album')),
        ]);
    }

    public function save(Request $request, $album_id)
    {
        $album = $this->client->getAlbum($album_id);
        $artist_image = $this->client->getArtist($album['artist']['id'])['image'];

        // Create Artist
        $artist = Artist::where('artist_id', $album['artist']['id'])->first();
        if (! $artist) {
            $artist = Artist::create([
                'name' => $album['artist']['name'],
                'image' => $artist_image ?: 'https://placehold.co/500',
                'artist_id' => $album['artist']['id'],
            ]);
        }

        // Create Album and related Tracks
        $album_entry = Album::where('album_id', $album['id'])->first();
        $previously_reviewed = !! $album_entry;

        if (! $previously_reviewed) {
            $album_entry = Album::create([
                'title' => $album['title'],
                'image' => $album['image'] ?: 'https://placehold.co/500',
                'type' => $album['type'] === 'album' ? 'Album' : 'EP',
                'album_id' => $album['id'],
                'artist_id' => $artist->id,
            ]);
        }

        foreach ($album['tracks'] as $track) {
            Track::updateOrCreate(
                [
                    'track_id' => $track['id'],
                    'album_id' => $album_entry->id,
                    'name' => $track['name'],
                    'image' => $album['image'],
                    'artists' => $track['artists']->implode(','),
                    'duration' => $track['duration'],
                ],
                [
                    'rating' => $request[$track['id']],
                ],
            );
        }

        return redirect('/');
    }

    public function index()
    {
        $albums = Album::orderBy('created_at', 'desc')
            ->get()
            ->collect()
            ->map(function ($album) {
                $avg_rating = Track::whereAlbumId($album['id'])->avg('rating');

                return [
                    'id' => $album['id'],
                    'rating'=> number_format(round($avg_rating, 1), 1),
                    'image' => $album['image'],
                ];
            });

        return view('reviews.index', [
            'albums' => $albums,
            'heading' => 'All Reviews'
        ]);
    }

    public function analysis($album_id)
    {
        $album = Album::query()->whereId($album_id)->first();
        $tracks = Track::query()->whereAlbumId($album_id)->get();

        $mean = round($tracks->avg('rating'), 2);
        $core_stats = $this->calculateCoreStatistics($tracks, $mean);
        $line_chart_data = $this->createLineChartData($tracks, $mean);
        $bar_chart_data = $this->createBarChartData($tracks);
        $artist_stats = $this->calculateArtistStatistics($album->artist_id);

        $top_tracks = $tracks->sortByDesc('rating')->take(3)->values();

        return view('reviews.analysis', [
            'album' => $album,
            'tracks' => $tracks->collect(),
            'heading' => $album->title,
            'line_chart_data' => $line_chart_data,
            'bar_chart_data' => $bar_chart_data,
            'core_stats' => $core_stats,
            'artist' => $artist_stats,
            'top_tracks' => $top_tracks,
        ]);
    }
}
