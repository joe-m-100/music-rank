<?php

namespace App\Http\Controllers;

use App\Services\SpotifyClient;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(
        protected SpotifyClient $client
    )
    {
    }

    // Artist search
    public function review(Request $request)
    {
        // $album = json_decode($request->input('data'), true);

        return view('review-album', [
            'album' => $this->client->getAlbum($request->query('album')),
        ]);
    }
}
