<?php

namespace Modules\Movies\Jobs;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Movies\Contracts\MoviesRepositoryInterface;
use Modules\Movies\Entities\Movie;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class ImportMovies implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public const KEY = 'movies_sync_interval';

    /** @var MoviesRepositoryInterface */
    protected $repository;
    /** @var int */
    protected $page;

    public function __construct(MoviesRepositoryInterface $repository, int $page = 1)
    {
        $this->repository = $repository;
        $this->page = $page;
        $nextRun = $this->getNextRun();
        if ($nextRun->gt(now())) {
            exit('Not yet');
        }
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $res = $this->sendRequest();

            if (Response::HTTP_OK === $res->getStatusCode()) {
                $this->handleResponse($res);
            }
        } catch (GuzzleException $ex) {
            Log::error($ex, ['file' => __FILE__, 'line' => __LINE__]);

            report($ex);
        }
    }

    /**
     * @throws GuzzleException
     *
     * @return ResponseInterface
     */
    protected function sendRequest(): ResponseInterface
    {
        $client = new Client();

        return $client->request(
            'GET',
            $this->getUrl(),
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]
        );
    }

    protected function getNextRun(): Carbon
    {
        $lastExecutionTime = $this->getLastExecutionTime(self::KEY);

        return $lastExecutionTime->addMinutes((int) config('movies.interval_minutes'));
    }

    protected function getUrl(): string
    {
        $baseUrl = config('movies.api_url');
        $key = config('movies.api_key');

        return "{$baseUrl}/movie/upcoming?api_key={$key}&page={$this->page}";
    }

    protected function handleResponse($response)
    {
        $contents = $response->getBody()->getContents();

        DB::beginTransaction();
        try {
            $rows = json_decode($contents, false, 512, JSON_THROW_ON_ERROR);

            Collection::wrap($rows->results)->each(function ($row) {
                $this->repository->updateOrInsertMovie($row->id, $row);
                $this->syncGenres($row->id, $row->genre_ids);
            });

            DB::commit();
            $this->setLastExecutionTime(self::KEY, now());
        } catch (Exception $e) {
            Log::error($e, ['file' => __FILE__, 'line' => __LINE__]);

            DB::rollBack();
        }
    }

    protected function syncGenres(int $id, array $genres): array
    {
        return Movie::find($id)->genres()->sync($genres);
    }

    protected function getLastExecutionTime($path): ?Carbon
    {
        if (! $path) {
            return null;
        }

        return cache()->remember($path, 60 * 60 * 100, static function () {
            return now();
        });
    }

    protected function setLastExecutionTime($path, Carbon $value): bool
    {
        if (! $path) {
            return false;
        }

        return cache()->put($path, $value, 60 * 60 * 100);
    }
}
