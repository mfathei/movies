<?php

namespace Modules\Movies\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'adult' => $this->adult,
            'backdrop_path' => $this->backdrop_path,
            'homepage' => $this->homepage,
            'imdb_id' => $this->imdb_id,
            'original_language' => $this->original_language,
            'original_title' => $this->original_title,
            'overview' => $this->overview,
            'popularity' => $this->popularity,
            'poster_path' => $this->poster_path,
            'budget' => $this->budget,
            'release_date' => $this->release_date->toDateString(),
            'revenue' => $this->revenue,
            'runtime' => $this->runtime,
            'status' => $this->status,
            'tagline' => $this->tagline,
            'title' => $this->title,
            'video' => $this->video,
            'vote_average' => $this->vote_average,
            'vote_count' => $this->vote_count,
            'genres' => GenreResource::collection($this->genres),
        ];
    }
}
