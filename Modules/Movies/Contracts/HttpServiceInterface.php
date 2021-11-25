<?php

declare(strict_types=1);

namespace Modules\Movies\Contracts;

interface HttpServiceInterface
{
    public function getData(): mixed;

    public function setBaseUri(string $baseUri): self;
}
