<?php

declare(strict_types=1);

namespace Modules\Movies\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Movies\Contracts\ResponseDecoderInterface;
use Psr\Http\Message\ResponseInterface;

class HttpService
{
    protected $client;
    protected $responseDecoder;
    protected $baseUri;
    protected $headers;

    public function __construct(Client $client, ResponseDecoderInterface $responseDecoder, string $baseUri, array $headers = [])
    {
        $this->client = $client;
        $this->responseDecoder = $responseDecoder;
        $this->baseUri = $baseUri;
        $this->headers = $headers;
    }

    public function getData(): mixed
    {
        try {
            $res = $this->sendRequest();

            if (Response::HTTP_OK === $res->getStatusCode()) {
                return $this->responseDecoder->decode($res);
            }
        } catch (GuzzleException $ex) {
            Log::error($ex);

            report($ex);
        }

        return null;
    }

    /**
     * @throws GuzzleException
     *
     * @return ResponseInterface
     */
    protected function sendRequest(): ResponseInterface
    {
        return $this->client->request(
            'GET',
            $this->baseUri,
            [
                'headers' => $this->headers ?? [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]
        );
    }
}
