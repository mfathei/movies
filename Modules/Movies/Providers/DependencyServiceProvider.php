<?php

namespace Modules\Movies\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Movies\Http\Controllers\Apis\Actions\ListMoviesAction;
use Modules\Movies\Http\Controllers\Apis\Responders\ListMoviesJsonResponder;
use Modules\Movies\Http\Controllers\Apis\Responders\ResponderInterface;

class DependencyServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->when(ListMoviesAction::class)
            ->needs(ResponderInterface::class)
            ->give(function () {
                return $this->app->make(ListMoviesJsonResponder::class);
            });
    }
}
