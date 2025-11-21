<?php

namespace App\Services\Statistics;

use App\Models\Track;

class PerfectTracks
{
    protected $title = 'Perfect Tracks';

    public function getTitle()
    {
        return $this->title;
    }

    public function getStatistic()
    {
        return Track::whereRating(10)->count();
    }
}
