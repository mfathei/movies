<?php

namespace Modules\Movies\Jobs;

use Illuminate\Support\Carbon;

class ImportTopRatedMovies extends ImportMovies
{
    protected function getUrl(): string
    {
        $baseUrl = config('movies.api_url');
        $key = config('movies.api_key');

        return "$baseUrl/movie/top_rated?api_key=$key&page=$this->page";
    }

    /**
     * Add 10 minutes delay, so we make sure top rated update after import movies
     * @return \Illuminate\Support\Carbon
     */
    protected function getNextRun(): Carbon
    {
        $lastExecutionTime = $this->getLastExecutionTime(self::KEY);
        return $lastExecutionTime->addMinutes((int)config('movies.interval_minutes'))->addMinutes(10);
    }
}
