<?php

namespace App\Services;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Track;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DataAnalyser
{
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

    private function createBarChartData(Collection $tracks)
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

    public function getCoreStatistics(Collection $tracks, float $mean) : array
    {
        // Calculate duration of album
        $duration = $tracks->sum('duration');

        $hours = (int) ($duration / 3600);
        $mins = (int) (($duration / 60) % 60);

        $album_duration = $hours ? $hours . ' hrs ' . $mins . ' mins' : $mins . ' mins';

        $range = $tracks->max('rating') - $tracks->min('rating');

        // Calculate Standard Deviation
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

    public function getArtistStatistics(int $artist_id) : array
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

    public function getGlobalStatistics() : array
    {
        return [
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
                'value' => Album::query()->withAvg('tracks', 'rating')->orderByDesc('tracks_avg_rating')->first()?->title ?: 'None'
            ],
            [
                'title' => 'Perfect Tracks',
                'value' => Track::whereRating(10)->count()
            ],

        ];
    }


    public function getChartData(Collection $tracks, float $mean) : array
    {
        return [
            'line_chart' => $this->createLineChartData($tracks, $mean),
            'bar_chart' => $this->createBarChartData($tracks)
        ];
    }

    public function getFeaturedArtists(Collection $tracks, string $artist_name): Collection
    {
        return $tracks->sortByDesc('rating')
            ->select('artists')
            ->flatten()
            ->flatMap(function ($artists) use ($artist_name) {
                return array_diff(explode(';', $artists), [$artist_name]);
            })
            ->unique()
            ->filter()
            ->take(5)
            ->values();
    }
}
