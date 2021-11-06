<?php

namespace Modules\Movies\Http\Controllers\Apis\Responders;

use Illuminate\Support\Collection;

interface ResponderInterface
{
    public function send(Collection $data);
}
