<?php

declare(strict_types=1);

namespace Modules\Movies\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface GenresRepositoryInterface
{
    public function updateOrInsertGenres(int $id, \stdClass $row): Builder;
}
