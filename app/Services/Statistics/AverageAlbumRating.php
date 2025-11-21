<?php

namespace App\Services\Statistics;

use App\Models\Album;
use Illuminate\Database\Eloquent\Builder;

class AverageAlbumRating
{
    protected $title = 'Average Album Rating';

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
        if (! $this->query) {
            return 'UNSET QUERY';
        }

        return round($this->query->withAvg('tracks', 'rating')->get()->avg('tracks_avg_rating'), 2);
    }
}
