<?php

namespace App\Services\Statistics;

use App\Models\Track;

class TrackReviews
{
    protected $title = 'Total Tracks Reviewed';

    public function getTitle()
    {
        return $this->title;
    }

    public function getStatistic()
    {
        return Track::get()->count();
    }
}
