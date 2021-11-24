<?php

namespace Modules\Movies\Tests\Unit\Console\Commands;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Modules\Movies\Jobs\ImportMovies;
use Modules\Movies\Utilities\ManagesIntervalRun;
use RuntimeException;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class ImportMoviesCommandTest extends TestCase
{
    /**
     * Test import movies command.
     */
    public function testImportMoviesBeforeNextRun()
    {
        Bus::fake();

        // Mock
        $this->createManagesIntervalRunMock(now()->addDay());

        // Assert
        $this->expectException(RuntimeException::class);
        $this->artisan('movies:import-movies');
    }

    /**
     * Test import movies command.
     */
    public function testImportMoviesAfterNextRun()
    {
        Bus::fake();

        // Mock
        $this->createManagesIntervalRunMock(now()->subMinute());

        // Assert
        $this->artisan('movies:import-movies');
        Bus::assertDispatched(ImportMovies::class);
    }

    protected function createManagesIntervalRunMock(Carbon $time)
    {
        $mock = $this->getMockBuilder(ManagesIntervalRun::class)->onlyMethods(['getNextRun'])->getMock();
        $mock->method('getNextRun')->willReturn($time);
        $this->app->instance(ManagesIntervalRun::class, $mock);
    }
}
