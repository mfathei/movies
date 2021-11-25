<?php

declare (strict_types = 1);

namespace Modules\Movies\Tests\Unit\Jobs;

use Illuminate\Support\Facades\Bus;
use Modules\Movies\Contracts\MoviesRepositoryInterface;
use Modules\Movies\Jobs\ImportMovies;
use Modules\Movies\Utilities\ManagesIntervalRun;
use Tests\TestCase;

class ImportMoviesTest extends TestCase
{
    public function testImportMoviesIsDispatched(): void
    {
        // Mock
        Bus::fake();
        $intervalRunMock = $this->getMockBuilder(ManagesIntervalRun::class)
            ->onlyMethods(['getNextRun', 'setLastExecutionTime'])
            ->getMock();

        $intervalRunMock->method('getNextRun')->willReturn(now()->addDays());

        $repositoryMock = $this->getMockBuilder(MoviesRepositoryInterface::class)
            ->onlyMethods(['updateOrInsertMovie', 'syncGenres', 'filterMoviesByGenres', 'addSortColumns', 'getQuery'])
            ->getMock();

        // Dispatch
        ImportMovies::dispatch(1, $intervalRunMock, $repositoryMock);

        // Assert
        Bus::assertDispatched(ImportMovies::class);
    }
}
