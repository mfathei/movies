<?php

namespace Modules\Movies\Http\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Modules\Movies\Contracts\MoviesRepositoryInterface;
use Modules\Movies\Entities\Movie;
use Modules\Movies\Http\Responders\ResponderInterface;
use Modules\Movies\Http\Requests\ListMoviesRequest;

class ListMoviesAction
{
    public const SORTABLE_FIELDS = [
        'popular' => 'popularity',
        'rated' => 'vote_average',
    ];

    /**
     * @var MoviesRepositoryInterface
     */
    protected $repository;
    /**
     * @var \Modules\Movies\Http\Responders\ResponderInterface
     */
    protected $responder;
    /**
     * @var \Modules\Movies\Http\Requests\ListMoviesRequest
     */
    protected $request;

    public function __construct(ListMoviesRequest $request, MoviesRepositoryInterface $repository, ResponderInterface $responder)
    {
        $this->repository = $repository;
        $this->request = $request;
        $this->responder = $responder;
    }

    public function __invoke()
    {
        $query = $this->repository->filterMoviesByGenres($this->request)->addSortColumns($this->parseSortParameters($this->request))->getQuery();

        return $this->responder->send($query->get());
    }

    protected function parseSortParameters(Request $request): Collection
    {
        $orderBy = collect();
        collect($request->all())->each(function ($param, $key) use ($orderBy) {
            if (count($field = explode('|', $key)) > 1 && ($newName = $this->replaceSortField($field[0]))) {
                $orderBy->push(['key' => $newName, 'value' => $field[1] ?? 'asc']);
            }
        });

        return $orderBy;
    }

    protected function replaceSortField(string $name): ?string
    {
        return self::SORTABLE_FIELDS[$name] ?? null;
    }
}
