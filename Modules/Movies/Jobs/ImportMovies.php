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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Movies\Contracts\MoviesRepositoryInterface;
use Modules\Movies\Contracts\ResponseDecoderInterface;
use Modules\Movies\Services\HttpService;
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

    /** @var HttpService */
    protected $httpService;

    /** @var ResponseDecoderInterface */
    protected $responseDecoder;

    /** @var ManagesIntervalRun */
    protected $intervalManager;

    public function __construct(int $page, ManagesIntervalRun $intervalManager, MoviesRepositoryInterface $repository, ResponseDecoderInterface $responseDecoder)
    {
        $this->repository = $repository;
        $this->intervalManager = $intervalManager;
        $this->page = $page;
        $this->responseDecoder = $responseDecoder;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $this->httpService = new HttpService(new Client(), $this->responseDecoder, $this->getUrl());

            $res = $this->httpService->getData();

            $this->handleResponse($res);
        } catch (GuzzleException $ex) {
            Log::error($ex);

            report($ex);
        }
    }

    protected function getUrl(): string
    {
        $baseUrl = config('movies.api_url');
        $key = config('movies.api_key');

        return "{$baseUrl}/movie/upcoming?api_key={$key}&page={$this->page}";
    }

    protected function handleResponse($rows)
    {
        DB::beginTransaction();
        try {
            Collection::wrap($rows->results)->each(function ($row) {
                $this->repository->updateOrInsertMovie($row->id, $row);
                $this->repository->syncGenres($row->id, $row->genre_ids);
            });

            DB::commit();
            $this->intervalManager->setLastExecutionTime($this->intervalManager::KEY, now());
        } catch (Exception $e) {
            Log::error($e);

            DB::rollBack();
        }
    }
}
