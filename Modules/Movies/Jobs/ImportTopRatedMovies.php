<?php

namespace Modules\Movies\Jobs;

class ImportTopRatedMovies extends ImportMovies
{
    public function getUrl(): string
    {
        $baseUrl = config('movies.api_url');
        $key = config('movies.api_key');

        return "{$baseUrl}/movie/top_rated?api_key={$key}&page={$this->page}";
    }
}
