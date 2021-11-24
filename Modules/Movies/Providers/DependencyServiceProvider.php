<?php

namespace Modules\Movies\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Movies\Contracts\MoviesRepositoryInterface;
use Modules\Movies\Http\Actions\ListMoviesAction;
use Modules\Movies\Http\Responders\ListMoviesJsonResponder;
use Modules\Movies\Http\Responders\ResponderInterface;
use Modules\Movies\Repositories\MoviesRepository;

class DependencyServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->when(ListMoviesAction::class)
            ->needs(ResponderInterface::class)
            ->give(function () {
                return $this->app->make(ListMoviesJsonResponder::class);
            });

        $this->app->bind(MoviesRepositoryInterface::class, MoviesRepository::class);
    }
}
