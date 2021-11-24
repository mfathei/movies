<?php

namespace Modules\Movies\Console;

use Modules\Movies\Jobs\ImportTopRatedMovies;
use Modules\Movies\Utilities\ManagesIntervalRun;

class ImportTopRatedMoviesCommand extends ImportMoviesCommand
{
    protected $signature = 'movies:import-top-rated-movies';
    protected $description = 'Import top rated movies';

    /**
     * Execute the console command.
     *
     * @param ManagesIntervalRun $intervalManager
     *
     * @return mixed
     */
    public function handle(ManagesIntervalRun $intervalManager)
    {
        $intervalManager->checkNextRun();
        for ($page = 1; $page <= $this->pages; $page++) {
            dispatch(new ImportTopRatedMovies($page));
        }
    }
}
