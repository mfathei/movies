<?php

declare(strict_types=1);

namespace Modules\Movies\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface MoviesRepositoryInterface
{
    public function updateOrInsertMovie(int $id, \stdClass $row): Builder;

    public function filterMoviesByGenres(Request $request): self;

    public function addSortColumns(Collection $columns): self;

    public function getQuery(): Builder;

    public function syncGenres(int $id, array $genres): array;
}
