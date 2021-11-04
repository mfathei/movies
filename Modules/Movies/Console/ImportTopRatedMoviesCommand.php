<?php

namespace Modules\Movies\Console;

use Modules\Movies\Jobs\ImportTopRatedMovies;

class ImportTopRatedMoviesCommand extends ImportMoviesCommand
{
    protected $signature = 'movies:import-top-rated-movies';
    protected $description = 'Import top rated movies';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        for ($page = 1; $page <= $this->pages; $page++) {
            dispatch(new ImportTopRatedMovies($page));
        }
    }
}
