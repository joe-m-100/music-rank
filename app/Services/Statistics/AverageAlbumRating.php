<?php

namespace App\Services\Statistics;

use App\Models\Album;

class AverageAlbumRating
{
    protected $title = 'Average Album Rating';

    public function getTitle()
    {
        return $this->title;
    }

    public function getStatistic()
    {
        return round(Album::withAvg('tracks', 'rating')->get()->avg('tracks_avg_rating'), 2);
    }
}
