<?php

namespace Modules\Movies\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Genre extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'updated_at'
    ];

    protected static function newFactory()
    {
        return \Modules\Movies\Database\factories\GenreFactory::new();
    }

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'movie_genres')->withTimestamps();
    }
}
