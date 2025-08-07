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

        return view('search.results', [
            'results' => $albums,
            'heading' => 'Available Albums',
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
