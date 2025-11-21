<?php

namespace App\Services\Statistics;

use App\Models\Album;
use Illuminate\Database\Eloquent\Builder;

class ExtendedPlayReviews
{
    protected $title = 'Extended Plays Reviewed';

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

        return $this->query->whereType('EP')->get()->count();
    }
}
