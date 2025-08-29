<?php

namespace App\Http\Controllers;

use App\Services\SpotifyClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpotifyController extends Controller
{
    public function __construct(
        protected SpotifyClient $client
    )
    {
    }

    // Retreives Artists from API and loads them into the search results template
    public function artist(Request $request)
    {
        $query = $request->input('artist');
        $results = $this->client->getArtists($query);

        $heading = str('Results for "?"')->replaceArray('?', [$query]);

        return view('search.results', [
            'results' => $results,
            'heading' => $heading,
        ]);
    }

    public function albums($artist_id)
    {
        $albums = $this->client->getArtistAlbums($artist_id);
        $eps = $this->client->getArtistEPs($artist_id);

        $all_music = array_merge($albums, $eps);

        return view('search.results', [
            'results' => $all_music,
            'heading' => 'Available Albums/EPs',
        ]);
    }

    public function tracks($album_id)
    {
        $album = $this->client->getAlbum($album_id);

        return view('search.tracks-overview', [
            'album' => $album,
            'heading' => $album['title'],
        ]);
    }

    // Artist search
    public function search(Request $request)
    {
        if ($request->query('artist')) {
            return $this->artist($request);
        }

        return view('search.search');
    }
}
