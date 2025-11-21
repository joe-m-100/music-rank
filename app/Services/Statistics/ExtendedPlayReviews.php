<?php

namespace App\Services\Statistics;

use App\Models\Album;

class ExtendedPlayReviews
{
    protected $title = 'Extended Plays Reviewed';

    public function getTitle()
    {
        return $this->title;
    }

    public function getStatistic()
    {
        return Album::whereType('EP')->get()->count();
    }
}
