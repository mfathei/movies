<?php

declare(strict_types=1);

namespace Modules\Movies\Contracts;

use GuzzleHttp\Client;

interface HttpServiceInterface
{
    public function __construct(Client $client, ResponseDecoderInterface $responseDecoder, string $baseUri, array $headers = []);

    public function getData(): mixed;

    public function setBaseUri(string $baseUri): self;
}
