<?php

namespace Modules\Movies\Console;

use Illuminate\Console\Command;
use Modules\Movies\Jobs\ImportGenres;

class ImportGenresCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movies:import-genres';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all Genres from our api';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dispatch(new ImportGenres());
    }
}
