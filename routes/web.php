<?php

use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SpotifyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('homepage');
});

Route::get('/test', function () {
    return view('test');
});

Route::get('/not-found', function () {
    return view('not-found');
});

// Search pages
Route::controller(SpotifyController::class)->group(function () {
    Route::get('/search', 'search');
    Route::get('/search/{artist_id}', 'albums');
    Route::get('/search/tracks/{album_id}', 'tracks');
});

// Search pages
Route::controller(ReviewController::class)->group(function () {
    Route::post('/review', 'review');
});