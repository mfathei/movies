<?php

namespace Modules\Movies\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Genre extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name'
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
