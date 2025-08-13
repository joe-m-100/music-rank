<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpotifyController extends Controller
{
    private function getAccessToken()
    {
        $clientID = config('services.spotify.client_id');
        $clientSecret = config('services.spotify.client_secret');

        $auth_response = Http::asForm()->withBasicAuth($clientID, $clientSecret)->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'client_credentials',
        ]);

        $access_token = $auth_response->json()['access_token'];

        return $access_token;
    }

    private function getAlbum(string $id)
    {
        $access_token = $this->getAccessToken();

        $response = Http::withToken($access_token)->get('https://api.spotify.com/v1/albums/'. $id, [
            'market' => 'GB',
        ]);



        if ($response->successful())
        {
            $tracks = [];
            $img = $response['images'] ? $response['images'][0]['url'] : null;

            $album = [
                'image' => $img,
                'title' => $response['name'],
                'artist' => $response['artists'][0]
            ];

            foreach ($response['tracks']['items'] as $track)
            {
                $duration = $track['duration_ms'] / 1000;
                $artists = [];

                foreach ($track['artists'] as $artist)
                {
                    $artists[] = $artist['name'];
                }

                $tracks[] = [
                    'name' => $track['name'],
                    'artists' => $artists,
                    'duration' => $duration,
                    'id' => $track['id']
                ];
            }

            $album['tracks'] = $tracks;
        }

        return $album;
    }

    private function getArtistEPs(string $id)
    {
        $access_token = $this->getAccessToken();

        $offset = 0;
        $finished = false;
        $results = [];

        while (! $finished) {
            $response = Http::withToken($access_token)->get('https://api.spotify.com/v1/artists/'. $id . '/albums', [
                'include_groups' => 'single',
                'market' => 'GB',
                'limit' => 50,
                'offset' => $offset
            ]);

            if ($response->successful())
            {
                $singles = $response['items'];

                foreach ($singles as $single)
                {
                    if ($single['total_tracks'] >= 3) {

                        $img = $single['images'] ? $single['images'][0]['url'] : null;

                        $results[] = [
                            'name' => $single['name'],
                            'type' => 'EP',
                            'image' => $img,
                            'id' => $single['id']
                        ];
                    }
                }
            }

            if (count($singles) === 50) {
                $offset += 50;
            }
            else {
                $finished = true;
            }
        }

        return $results;
    }


    // Get an artist's albums
    private function getArtistAlbums(string $id)
    {
        $access_token = $this->getAccessToken();

        $response = Http::withToken($access_token)->get('https://api.spotify.com/v1/artists/'. $id . '/albums', [
            'include_groups' => 'album',
            'market' => 'GB',
            'limit' => 50
        ]);

        $results = [];
        if ($response->successful())
        {
            $albums = $response['items'];

            foreach ($albums as $album)
            {
                $img = $album['images'] ? $album['images'][0]['url'] : null;

                $results[] = [
                    'name' => $album['name'],
                    'type' => 'Album',
                    'image' => $img,
                    'id' => $album['id']
                ];
            }
        }
        return $results;
    }

    // Retreives Artists from API and loads them into the search results template
    public function artist(Request $request)
    {
        $access_token = $this->getAccessToken();

        $query = $request->input('artist');

        $response = Http::withToken($access_token)->get('https://api.spotify.com/v1/search', [
            'q' => $query,
            'type' => 'artist',
            'limit' => 10
        ]);

        $artists = $response['artists']['items'];

        $results = [];

        foreach ($artists as $artist)
        {
            $img = $artist['images'] ? $artist['images'][0]['url'] : null;

            $results[] = [
                'name' => $artist['name'],
                'type' => 'Artist',
                'image' => $img,
                'id' => $artist['id']
            ];
        }

        $heading = str('Results for "?"')->replaceArray('?', [$query]);

        return view('search.results', [
            'results' => $results,
            'heading' => $heading,
        ]);
    }

    public function albums($artist_id)
    {

        $albums = $this->getArtistAlbums($artist_id);
        $eps = $this->getArtistEPs($artist_id);

        $all_music = array_merge($albums, $eps);

        return view('search.results', [
            'results' => $all_music,
            'heading' => 'Available Albums/EPs',
        ]);
    }

    public function tracks($album_id)
    {
        $album = $this->getAlbum($album_id);

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
