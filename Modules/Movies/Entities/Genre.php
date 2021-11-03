<?php

namespace Modules\Movies\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}
