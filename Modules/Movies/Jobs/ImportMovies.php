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
use Modules\Movies\Entities\Movie;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class ImportMovies implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $page;

    public function __construct(int $page = 1)
    {
        $this->page = $page;
    }

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
            "$baseUrl/movie/upcoming?api_key=$key&page=$this->page",
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

            Collection::wrap($rows->results)->each(function ($row) {
                Movie::updateOrInsert(
                    ['id' => $row->id],
                    [
                        "adult" => $row->adult,
                        "backdrop_path" => $row->backdrop_path,
                        "original_language" => $row->original_language,
                        "original_title" => $row->original_title,
                        "overview" => $row->overview,
                        "popularity" => $row->popularity,
                        "poster_path" => $row->poster_path,
                        "release_date" => $row->release_date,
                        "title" => $row->title,
                        "video" => $row->video,
                        "vote_average" => $row->vote_average,
                        "vote_count" => $row->vote_count,
                        "status" => 'Released',
                    ]
                );

                Movie::find($row->id)->genres()->sync($row->genre_ids);
            });

            DB::commit();
        } catch (Exception $e) {
            Log::error($e, ['file' => __FILE__, 'line' => __LINE__]);

            DB::rollBack();
        }
    }
}
