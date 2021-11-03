<?php

namespace Modules\Movies\Jobs;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Movies\Entities\Genre;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class ImportGenres implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $res = $this->sendRequest();

            if ($res->getStatusCode() === Response::HTTP_OK) {
                $this->handleResponse($res);
            }
        } catch (GuzzleException $ex) {
            Log::error($ex, ['file' => __FILE__, 'line' => __LINE__]);

            report($ex);
        }
    }

    /**
     * @return ResponseInterface
     * @throws GuzzleException
     */
    protected function sendRequest(): ResponseInterface
    {
        $client = new Client();
        $baseUrl = config('movies.api_url');
        $key = config('movies.api_key');

        return $client->request('GET',
            "$baseUrl/genre/movie/list?api_key=$key",
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
            ]
        );
    }

    protected function handleResponse($response)
    {
        $contents = $response->getBody()->getContents();

        DB::beginTransaction();
        try {
            $rows = json_decode($contents, false, 512, JSON_THROW_ON_ERROR);

            Collection::wrap($rows->genres)->each(function ($row) {
                Genre::create([
                    'id' => $row->id,
                    'name' => $row->name,
                ]);
            });

            DB::commit();
        } catch (Exception $e) {
            Log::error($e, ['file' => __FILE__, 'line' => __LINE__]);

            DB::rollBack();
        }
    }
}
