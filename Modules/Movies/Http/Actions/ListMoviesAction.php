<?php

namespace Modules\Movies\Http\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
     * @var \Modules\Movies\Http\Responders\ResponderInterface
     */
    protected $responder;
    /**
     * @var \Modules\Movies\Http\Requests\ListMoviesRequest
     */
    protected $request;

    public function __construct(ListMoviesRequest $request, ResponderInterface $responder)
    {
        $this->request = $request;
        $this->responder = $responder;
    }

    public function __invoke()
    {
        $query = Movie::query()->when($this->request->category_id, function ($q, $category_id) {
            return $q->whereHas('genres', function ($query) use ($category_id) {
                return $query->where('genres.id', $category_id);
            });
        });

        $this->parseSortParameters($this->request)->each(function ($order) use ($query) {
            $query->orderBy($order['key'], $order['value']);
        });

        return $this->responder->send($query->get());
    }

    protected function parseSortParameters(Request $request): Collection
    {
        $orderBy = collect();
        collect($request->all())->each(function ($param, $key) use ($orderBy) {
            if (count($field = explode('|', $key)) > 1 && $newName = $this->replaceSortField($field[0])) {
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
