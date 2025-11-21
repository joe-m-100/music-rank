<?php

namespace App\Services\Statistics;

use App\Models\Album;
use Illuminate\Database\Eloquent\Builder;

class AlbumReviews
{
    protected $title = 'Albums Reviewed';

    protected ?Builder $query = null;

    public function setQuery(Builder $query)
    {
        $this->query = $query;
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

        return $this->query->whereType('Album')->get()->count();
        // return Album::whereType('Album')->get()->count();
    }
}
