<?php

declare(strict_types=1);

namespace Modules\Movies\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Modules\Movies\Contracts\GenresRepositoryInterface;
use Modules\Movies\Entities\Genre;

class GenresRepository implements GenresRepositoryInterface
{
    public function updateOrInsertGenres(int $id, \stdClass $row): Builder
    {
        return Genre::updateOrInsert(
            ['id' => $id],
            [
                'updated_at' => now(),
                'name' => $row->name,
            ]
        );
    }
}
