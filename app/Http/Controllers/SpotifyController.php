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

    private function getArtistAlbums(string $id)
    {
        $access_token = $this->getAccessToken();

        $response = Http::withToken($access_token)->get('https://api.spotify.com/v1/artists/'. $id . '/albums', [
            'include_groups' => 'album',
            'market' => 'GB',
            'limit' => 50
        ]);

        dd($response);

        return $response;
    }

    public function artist(Request $request)
    {
        $access_token = $this->getAccessToken();

        $query = $request->input('artist');

        $response = Http::withToken($access_token)->get('https://api.spotify.com/v1/search', [
            'q' => $query,
            'type' => 'artist',
            'limit' => 5
        ]);

        $artists = $response['artists']['items'];

        return view('search.results', [
            'results' => $artists,
            'query' => $query,
        ]);



    }

    public function albums($id)
    {
        $albums = $this->getArtistAlbums($id);

        return view('search.results', [
            'results' => $albums,
            'query' => 'Ablums',
        ]);
    }


    public function search(Request $request)
    {
        if ($request->query('artist')) {
            return $this->artist($request);
        }

        return view('search.search');
    }
}
