<?php

namespace Modules\Movies\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
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
}
