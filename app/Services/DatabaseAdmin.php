<?php

namespace App\Services;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Track;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class DatabaseAdmin
{
    public function __construct(
        protected SpotifyClient $client,
    )
    {
    }

    public function getArtist(int|string $id) : Artist|null
    {
        $column = 'id';
        if (gettype($id) === 'string') {
            $column = 'artist_id';
        }

        return Artist::where($column, $id)->first();
    }

    public function getAlbum(int|string $id) : Album|null
    {
        $column = 'id';
        if (gettype($id) === 'string') {
            $column = 'album_id';
        }

        return Album::where($column, $id)->first();
    }

    public function getAlbumTracks(int $id) : Collection|null
    {
        return Track::query()->whereAlbumId($id)->get();
    }

    public function saveAlbum(string $album_id, $ratings) : void
    {
        $album = $this->client->getAlbum($album_id);
        $artist_image = $this->client->getArtist($album['artist']['id'])['image'];

        // Create Artist
        $artist = $this->getArtist($album['artist']['id']);
        if (! $artist) {
            $artist = Artist::create([
                'name' => $album['artist']['name'],
                'image' => $artist_image ?: 'https://placehold.co/500',
                'artist_id' => $album['artist']['id'],
            ]);
        }

        // Create Album and related Tracks
        $album_entry = $this->getAlbum($album['id']);
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
                    'artists' => $track['artists']->implode(';'),
                    'duration' => $track['duration'],
                ],
                [
                    'rating' => $ratings[$track['id']],
                ],
            );
        }
    }

    public function getAllReviews() : Collection
    {
        return Album::orderBy('created_at', 'desc')
            ->get()
            ->collect()
            ->map(function ($album) {
                $avg_rating = Track::whereAlbumId($album['id'])->avg('rating');

                return [
                    'id' => $album['id'],
                    'rating'=> number_format(round($avg_rating, 1), 1),
                    'image' => $album['image'],
                ];
            });
    }
}
