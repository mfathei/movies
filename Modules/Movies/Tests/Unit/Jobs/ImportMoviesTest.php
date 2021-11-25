<?php

declare(strict_types=1);

namespace Modules\Movies\Tests\Unit\Jobs;

use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Modules\Movies\Contracts\HttpServiceInterface;
use Modules\Movies\Contracts\MoviesRepositoryInterface;
use Modules\Movies\Contracts\ResponseDecoderInterface;
use Modules\Movies\Entities\Movie;
use Modules\Movies\Jobs\ImportMovies;
use Modules\Movies\Repositories\MoviesRepository;
use Modules\Movies\Services\HttpService;
use Modules\Movies\Utilities\ManagesIntervalRun;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class ImportMoviesTest extends TestCase
{
    use RefreshDatabase;

    public function testImportMoviesIsDispatched(): void
    {
        // Mock
        Bus::fake();
        $intervalRunMock = $this->getMockBuilder(ManagesIntervalRun::class)
            ->onlyMethods(['getNextRun', 'setLastExecutionTime'])
            ->getMock();

        $intervalRunMock->method('getNextRun')->willReturn(now()->addDays());

        $repositoryMock = $this->getMockBuilder(MoviesRepository::class)
            ->onlyMethods(['updateOrInsertMovie', 'syncGenres'])
            ->getMock();

        // Dispatch
        ImportMovies::dispatch(1, $intervalRunMock, $repositoryMock);

        // Assert
        Bus::assertDispatched(ImportMovies::class);
    }

    public function testImportMoviesWorksWithMock(): void
    {
        // Mock
        $intervalRunMock = $this->getMockBuilder(ManagesIntervalRun::class)
            ->onlyMethods(['getNextRun', 'setLastExecutionTime'])
            ->getMock();

        $intervalRunMock->method('getNextRun')->willReturn(now()->addDays());
        $this->app->instance(ManagesIntervalRun::class, $intervalRunMock);

        $httpServiceMock = $this->getMockBuilder(HttpService::class)
            ->onlyMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMock();

        $response = file_get_contents(__DIR__.'/../../Mocks/mocks-movies.json');
        $httpServiceMock->method('getData')->willReturn($this->app->make(ResponseDecoderInterface::class)->decode(new Response(200, [], $response)));
        $this->app->instance(HttpServiceInterface::class, $httpServiceMock);

        // Dispatch
        $job = new ImportMovies(1, app(ManagesIntervalRun::class), app(MoviesRepositoryInterface::class));
        $job->handle();

        // Assert
        static::assertSame(3, Movie::all()->count());
        tap(Movie::findOrFail(580489), function ($movie) {
            $this->assertFalse($movie->adult);
            $this->assertSame('/70nxSw3mFBsGmtkvcs91PbjerwD.jpg', $movie->backdrop_path);
            $this->assertSame('Updated to diffrentiate between mocking and real data', $movie->original_title);
        });
    }
}
