<?php

namespace App\Services\Statistics;

use App\Models\Track;
use Illuminate\Database\Eloquent\Builder;

class PerfectTracks
{
    protected $title = 'Perfect Tracks';

    protected ?Builder $query = null;

    public function setQuery(Builder $query)
    {
        $this->query = $query;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getStatistic()
    {
        return $this->query->whereRating(10)->count();
    }
}
