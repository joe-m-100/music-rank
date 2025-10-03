<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Track;
use App\Services\SpotifyClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ReviewController extends Controller
{
    public function __construct(
        protected SpotifyClient $client
    )
    {
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
}
