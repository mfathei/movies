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
use Modules\Movies\Utilities\ManagesIntervalRun;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class ImportMovies implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var MoviesRepositoryInterface */
    protected $repository;

    /** @var int */
    protected $page;

    public function __construct(int $page, ManagesIntervalRun $intervalManager, MoviesRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->intervalManager = $intervalManager;
        $this->page = $page;
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
            Log::error($ex, compact(__FILE__, __LINE__));

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
                $this->repository->syncGenres($row->id, $row->genre_ids);
            });

            DB::commit();
            $this->intervalManager->setLastExecutionTime($this->key, now());
        } catch (Exception $e) {
            Log::error($e, compact(__FILE__, __LINE__));

            DB::rollBack();
        }
    }
}
