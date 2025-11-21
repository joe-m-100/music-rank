<?php

namespace App\Services\Statistics;

use App\Models\Track;

class AverageAlbumRating
{
    protected $title = 'Average Album Rating';

    public function getTitle()
    {
        return $this->title;
    }

    public function getStatistic()
    {
        return Track::get()->count();
    }
}
