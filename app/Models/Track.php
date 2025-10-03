<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Track extends Model
{
    /** @use HasFactory<\Database\Factories\TrackFactory> */
    use HasFactory;

    protected $fillable = ['album_id', 'name', 'image', 'artists', 'duration', 'rating', 'track_id'];

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }
}
