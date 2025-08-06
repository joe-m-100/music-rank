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

    // private function getArtistAlbums($id)
    // {
    //     $access_token = $this->getAccessToken();

    //     // $query = $request->input('artist');

    //     $response = Http::withToken($access_token)->get('https://api.spotify.com/v1/search', [
    //         'q' => $query,
    //         'type' => 'artist',
    //         'limit' => 5
    //     ]);
    // }

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

    // public function albums($id)
    // {
    //     $albums = $this->getArtistAlbums($id);

    //     return view('search.results', [
    //         'results' => $albums,
    //         'query' => 'Ablums',
    //     ]);
    // }


    public function search()
    {
        return view('search.search');
    }
}
