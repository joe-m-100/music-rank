<?php

namespace App\Services\Statistics;

use App\Models\Album;

class AlbumReviews
{
    protected $title = 'Albums Reviewed';

    public function getTitle()
    {
        return $this->title;
    }

    public function getStatistic()
    {
        return Album::whereType('Album')->get()->count();
    }
}
