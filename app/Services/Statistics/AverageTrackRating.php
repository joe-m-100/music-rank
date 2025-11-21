<?php

namespace App\Services\Statistics;

use App\Models\Track;

class AverageTrackRating
{
    protected $title = 'Average Track Rating';

    public function getTitle()
    {
        return $this->title;
    }

    public function getStatistic()
    {
        return round(Track::get()->avg('rating'), 2);
    }
}
