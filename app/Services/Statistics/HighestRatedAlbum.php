<?php

namespace App\Services\Statistics;

use App\Models\Album;
use Illuminate\Database\Eloquent\Builder;

class HighestRatedAlbum
{
    protected $title = 'Highest Rated Album';

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
        return $this->query
            ->withAvg('tracks', 'rating')
            ->orderByDesc('tracks_avg_rating')
            ->first()?->title
            ?: 'None';
    }
}
