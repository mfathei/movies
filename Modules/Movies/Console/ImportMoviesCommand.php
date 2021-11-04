<?php

namespace Modules\Movies\Console;

use Illuminate\Console\Command;
use Modules\Movies\Jobs\ImportMovies;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ImportMoviesCommand extends Command
{
    public const ROWS_PER_PAGE = 20;

    protected $signature = 'movies:import-movies';
    protected $description = 'Import upcoming movies';
    /**
     * @var false|float
     */
    private $pages;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $count = max((integer) config('movies.seed_count'), self::ROWS_PER_PAGE);
        $this->pages = ceil($count / self::ROWS_PER_PAGE);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        for ($page = 1; $page <= $this->pages; $page++) {
            dispatch(new ImportMovies($page));
        }
    }
}
