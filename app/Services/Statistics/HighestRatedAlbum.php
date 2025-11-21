<?php

namespace App\Services\Statistics;

use App\Models\Album;

class HighestRatedAlbum
{
    protected $title = 'Highest Rated Album';

    public function getTitle()
    {
        return $this->title;
    }

    public function getStatistic()
    {
        return Album::query()
            ->withAvg('tracks', 'rating')
            ->orderByDesc('tracks_avg_rating')
            ->first()?->title
            ?: 'None';
    }
}
