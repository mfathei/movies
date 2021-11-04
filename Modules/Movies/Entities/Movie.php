<?php

namespace Modules\Movies\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Movie extends Model
{
    use HasFactory;

    protected $casts = [
        'popularity' => 'float',
        'vote_average' => 'float',
        'release_date' => 'date',
        'adult' => 'boolean',
        'video' => 'boolean',
        'budget' => 'integer',
        'revenue' => 'integer',
        'runtime' => 'integer',
        'vote_count' => 'integer',
    ];

    protected $fillable = [
        'id',
        'adult',
        'backdrop_path',
        'homepage',
        'imdb_id',
        'original_language',
        'original_title',
        'overview',
        'popularity',
        'poster_path',
        'budget',
        'release_date',
        'revenue',
        'runtime',
        'status',
        'tagline',
        'title',
        'video',
        'vote_average',
        'vote_count',
    ];

    protected static function newFactory()
    {
        return \Modules\Movies\Database\factories\MovieFactory::new();
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'movie_genres')->withTimestamps();
    }
}
