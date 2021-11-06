<?php

namespace Modules\Movies\Http\Responders;

use Illuminate\Support\Collection;

interface ResponderInterface
{
    public function send(Collection $data);
}
