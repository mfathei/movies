<?php

declare(strict_types=1);

namespace Modules\Movies\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Modules\Movies\Contracts\MoviesRepositoryInterface;
use Modules\Movies\Entities\Movie;

class MoviesRepository implements MoviesRepositoryInterface
{
    /**
     * @var Builder
     */
    private $query;

    public function updateOrInsertMovie(int $id, \stdClass $row): Builder
    {
        return Movie::updateOrInsert(
            ['id' => $id],
            [
                'adult' => $row->adult,
                'backdrop_path' => $row->backdrop_path,
                'original_language' => $row->original_language,
                'original_title' => $row->original_title,
                'overview' => $row->overview,
                'popularity' => $row->popularity,
                'poster_path' => $row->poster_path,
                'release_date' => $row->release_date,
                'title' => $row->title,
                'video' => $row->video,
                'vote_average' => $row->vote_average,
                'vote_count' => $row->vote_count,
                'status' => 'Released',
            ]
        );
    }

    public function filterMoviesByGenres(Request $request): MoviesRepositoryInterface
    {
        $this->query = Movie::query()->when($request->category_id, function ($q, $categoryId) {
            return $q->whereHas('genres', function ($query) use ($categoryId) {
                return $query->where('genres.id', $categoryId);
            });
        });

        return $this;
    }

    public function addSortColumns(Collection $columns): MoviesRepositoryInterface
    {
        $columns->each(function ($column) {
            $this->query->orderBy($column['key'], $column['value']);
        });

        return $this;
    }

    public function syncGenres(int $id, array $genres): array
    {
        return Movie::find($id)->genres()->sync($genres);
    }

    public function getQuery(): Builder
    {
        return $this->query;
    }
}
