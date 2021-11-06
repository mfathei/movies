<?php

namespace Modules\Movies\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Movies\Http\Actions\ListMoviesAction;
use Modules\Movies\Http\Responders\ListMoviesJsonResponder;
use Modules\Movies\Http\Responders\ResponderInterface;

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
