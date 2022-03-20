<?php

namespace Modules\Movies\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Movies\Contracts\GenresRepositoryInterface;
use Modules\Movies\Contracts\ResponseDecoderInterface;
use Modules\Movies\Services\HttpService;

class ImportGenres implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var HttpService */
    protected $httpService;

    /** @var ResponseDecoderInterface */
    protected $responseDecoder;

    /** @var GenresRepositoryInterface */
    protected $repository;

    public function __construct(GenresRepositoryInterface $genresRepository, ResponseDecoderInterface $responseDecoder)
    {
        $this->repository = $genresRepository;
        $this->responseDecoder = $responseDecoder;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $this->httpService = app(HttpService::class, ['baseUri' => $this->getUrl()]);

            $res = $this->httpService->getData();

            $this->handleResponse($res);
        } catch (Exception $ex) {
            Log::error($ex);

            report($ex);
        }
    }

    protected function getUrl(): string
    {
        $baseUrl = config('movies.api_url');
        $key = config('movies.api_key');

        return "{$baseUrl}/genre/movie/list?api_key={$key}";
    }

    protected function handleResponse($rows): void
    {
        DB::beginTransaction();
        try {
            Collection::wrap($rows->genres)->each(function ($row) {
                $this->repository->updateOrInsertGenres($row->id, $row);
            });

            DB::commit();
        } catch (Exception $e) {
            Log::error($e);

            DB::rollBack();
        }
    }
}
