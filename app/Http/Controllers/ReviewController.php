<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Track;
use App\Services\SpotifyClient;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ReviewController extends Controller
{
    public function __construct(
        protected SpotifyClient $client
    )
    {
    }

    public function globals()
    {
        // Highest Album Rating
        // Group tracks by album ID
        // Get AVG rating of groups
        // Get the highest rating
        // Find that album ID
        // Get the name of the album

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
}
