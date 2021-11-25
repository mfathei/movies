<?php

declare(strict_types=1);

namespace Modules\Movies\Contracts;

use Psr\Http\Message\ResponseInterface;

interface ResponseDecoderInterface
{
    public function decode(ResponseInterface $responseInterface): mixed;
}
