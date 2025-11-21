<?php

namespace App\Services\Statistics;

use App\Models\Track;
use Illuminate\Database\Eloquent\Builder;

class AverageTrackRating
{
    protected $title = 'Average Track Rating';

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
        return round($this->query->get()->avg('rating'), 2);
    }
}
