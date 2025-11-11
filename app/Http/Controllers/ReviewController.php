<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Track;
use App\Services\DataAnalyser;
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
        protected SpotifyClient $client,
        protected DataAnalyser $analyser
    )
    {
    }

    public function globals()
    {

        $stats = $this->analyser->getGlobalStatistics();

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
        $core_stats = $this->analyser->getCoreStatistics($tracks, $mean);
        $artist_stats = $this->analyser->getArtistStatistics($album->artist_id);
        $chart_data = $this->analyser->getChartData($tracks, $mean);
        $top_tracks = $tracks->sortByDesc('rating')->take(3)->values();

        return view('reviews.analysis', [
            'album' => $album,
            'tracks' => $tracks->collect(),
            'heading' => $album->title,
            'line_chart_data' => $chart_data['line_chart'],
            'bar_chart_data' => $chart_data['bar_chart'],
            'core_stats' => $core_stats,
            'artist' => $artist_stats,
            'top_tracks' => $top_tracks,
        ]);
    }
}
