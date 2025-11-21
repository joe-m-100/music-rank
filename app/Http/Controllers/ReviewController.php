<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Track;
use App\Services\DataAnalyser;
use App\Services\SpotifyClient;
use App\Services\DatabaseAdmin;
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
        protected DataAnalyser $analyser,
        protected DatabaseAdmin $admin
    )
    {
    }

    public function homepage()
    {
        return view('reviews.homepage');
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
        $this->admin->saveAlbum($album_id, $request);

        return redirect('/reviewed-albums');
    }

    public function index()
    {
        $albums = $this->admin->getAllReviews();

        return view('reviews.index', [
            'albums' => $albums,
            'heading' => 'All Reviews'
        ]);
    }

    public function analysis(int $album_id)
    {
        $album = $this->admin->getAlbum($album_id);
        $tracks = $this->admin->getAlbumTracks($album_id);

        $mean = round($tracks->avg('rating'), 2);
        $core_stats = $this->analyser->getCoreStatistics($tracks, $mean);
        $artist_stats = $this->analyser->getArtistStatistics($album->artist_id);
        $chart_data = $this->analyser->getChartData($tracks, $mean);
        $top_tracks = $tracks->sortByDesc('rating')->take(3)->values();

        $features = $this->analyser->getFeaturedArtists($tracks, $artist_stats['name']);

        return view('reviews.analysis', [
            'album' => $album,
            'tracks' => $tracks->collect(),
            'heading' => $album->title,
            'line_chart_data' => $chart_data['line_chart'],
            'bar_chart_data' => $chart_data['bar_chart'],
            'core_stats' => $core_stats,
            'artist' => $artist_stats,
            'top_tracks' => $top_tracks,
            'features' => $features,
        ]);
    }
}
