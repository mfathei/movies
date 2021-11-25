<?php

declare(strict_types=1);

namespace Modules\Movies\Utilities;

use Modules\Movies\Contracts\ResponseDecoderInterface;
use Psr\Http\Message\ResponseInterface;

class JsonResponseDecoder implements ResponseDecoderInterface
{
    public function decode(ResponseInterface $responseInterface): mixed
    {
        $contents = $responseInterface->getBody()->getContents();

        return json_decode($contents, false, 512, JSON_THROW_ON_ERROR);
    }
}
