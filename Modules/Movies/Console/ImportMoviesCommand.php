<?php

namespace Modules\Movies\Console;

use Illuminate\Console\Command;
use Modules\Movies\Contracts\MoviesRepositoryInterface;
use Modules\Movies\Contracts\ResponseDecoderInterface;
use Modules\Movies\Jobs\ImportMovies;
use Modules\Movies\Utilities\ManagesIntervalRun;

class ImportMoviesCommand extends Command
{
    public const ROWS_PER_PAGE = 20;

    protected $signature = 'movies:import-movies';
    protected $description = 'Import upcoming movies';
    /**
     * @var false|float
     */
    protected $pages;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
        $count = max((int) config('movies.seed_count'), self::ROWS_PER_PAGE);
        $this->pages = ceil($count / self::ROWS_PER_PAGE);
    }

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

        for ($page = 1; $page <= $this->pages; ++$page) {
            dispatch(new ImportMovies($page, $intervalManager, app(MoviesRepositoryInterface::class), app(ResponseDecoderInterface::class)));
        }
    }
}
