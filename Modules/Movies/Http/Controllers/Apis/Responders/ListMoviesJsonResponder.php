<?php

namespace Modules\Movies\Http\Controllers\Apis\Responders;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Modules\Movies\Transformers\MovieResource;

class ListMoviesJsonResponder implements ResponderInterface
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function send(Collection $data): AnonymousResourceCollection
    {
        return MovieResource::collection($data);
    }
}
