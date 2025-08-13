<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // Artist search
    public function review(Request $request)
    {
        $album = json_decode($request->input('data'), true);

        return view('review-album', [
            'album' => $album
        ]);
    }
}
