<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SpotifyClient
{
    public function client()
    {
        return Http::withToken($this->getAccessToken())->baseUrl('https://api.spotify.com');
    }

    protected function getAccessToken()
    {
        $clientID = config('services.spotify.client_id');
        $clientSecret = config('services.spotify.client_secret');

        $auth_response = Http::asForm()->withBasicAuth($clientID, $clientSecret)->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'client_credentials',
        ]);

        $access_token = $auth_response->json()['access_token'];

        return $access_token;
    }

    public function getArtists(string $query)
    {
        return Cache::flexible('spotify-query-'. $query, [now()->addHour(), now()->addWeek()], function () use ($query) {
            $response =  $this->client()->get('/v1/search', [
                'q' => $query,
                'type' => 'artist',
                'limit' => 10
            ]);

            if (! $response->successful()) {
                throw new \Exception('unable to find artists');
            }

            $results = collect($response['artists']['items'])
                        ->map(function ($artist) {
                            return [
                                'name' => $artist['name'],
                                'type' => 'Artist',
                                'image' => $artist['images'] ? $artist['images'][0]['url'] : null,
                                'id' => $artist['id']
                            ];
                        })
                        ->all();

            return $results;
        });
    }

    public function getAlbum(string $id)
    {
        return Cache::flexible('spotify-album-'. $id, [now()->addHour(), now()->addWeek()], function () use ($id) {
            $response =  $this->client()->get('/v1/albums/'. $id, [
                'market' => 'GB',
            ]);

            if (! $response->successful()) {
                throw new \Exception('unable to find album');
            }

            $img = $response['images'] ? $response['images'][0]['url'] : null;

            $album = [
                'image' => $img,
                'id' => $response['id'],
                'title' => $response['name'],
                'artist' => $response['artists'][0],
                'type' => $response['album_type'],
                'tracks' => collect($response['tracks']['items'])->map(fn ($track) => [
                                'id' => $track['id'],
                                'name' => $track['name'],
                                'duration' => $track['duration_ms'] / 1000,
                                'artists' => collect($track['artists'])->map(function ($artist) {
                                    return $artist['name'];
                                })
                            ]),
            ];

            return $album;
        });
    }

    public function getArtistEPs(string $id)
    {
        return Cache::flexible('spotify-artist-eps-'. $id, [now()->addHour(), now()->addWeek()], function () use ($id) {
            $offset = 0;
            $finished = false;
            $singles = [];

            while (! $finished) {
                $response =  $this->client()->get('/v1/artists/'. $id . '/albums', [
                    'include_groups' => 'single',
                    'market' => 'GB',
                    'limit' => 50,
                    'offset' => $offset
                ]);

                if (! $response->successful()) {
                    throw new \Exception('unable to find EPs');
                }

                $singles[] = $response['items'];

                if (count($response['items']) === 50) {
                    $offset += 50;
                }
                else {
                    $finished = true;
                }
            }

            $results = collect($singles)->flatten(1)
                        ->map(function ($single) {
                            if ($single['total_tracks'] >= 3) {
                                return [
                                    'name' => $single['name'],
                                    'type' => 'EP',
                                    'image' => $single['images'] ? $single['images'][0]['url'] : null,
                                    'id' => $single['id']
                                ];
                            }
                        })
                        ->filter()
                        ->values()
                        ->all();

            return $results;
        });
    }

    // Get an artist's albums
    public function getArtistAlbums(string $id)
    {
        return Cache::flexible('spotify-album-'. $id, [now()->addHour(), now()->addWeek()], function () use ($id) {
            $response =  $this->client()->get('/v1/artists/'. $id . '/albums', [
                'include_groups' => 'album',
                'market' => 'GB',
                'limit' => 50
            ]);

            if (! $response->successful()) {
                throw new \Exception('unable to find albums');
            }

            $results = collect($response['items'])
                        ->map(function ($album) {
                            return [
                                'name' => $album['name'],
                                'type' => 'Album',
                                'image' => $album['images'] ? $album['images'][0]['url'] : null,
                                'id' => $album['id']
                            ];
                        })
                        ->all();

            return $results;

        });
    }

    public function getArtist(string $id)
    {
        $response =  $this->client()->get('/v1/artists/'. $id, []);

        if (! $response->successful()) {
                throw new \Exception('unable to find albums');
        }

        $results = [
            'name' => $response['name'],
            'image' => $response['images'] ? $response['images'][0]['url'] : null
        ];

        return $results;
    }

}
