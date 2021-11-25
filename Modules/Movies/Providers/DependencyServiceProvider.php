<?php

namespace Modules\Movies\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Modules\Movies\Contracts\GenresRepositoryInterface;
use Modules\Movies\Contracts\HttpServiceInterface;
use Modules\Movies\Contracts\MoviesRepositoryInterface;
use Modules\Movies\Contracts\ResponseDecoderInterface;
use Modules\Movies\Http\Actions\ListMoviesAction;
use Modules\Movies\Http\Responders\ListMoviesJsonResponder;
use Modules\Movies\Http\Responders\ResponderInterface;
use Modules\Movies\Jobs\ImportMovies;
use Modules\Movies\Repositories\GenresRepository;
use Modules\Movies\Repositories\MoviesRepository;
use Modules\Movies\Services\HttpService;
use Modules\Movies\Utilities\JsonResponseDecoder;

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
        $this->app->bind(GenresRepositoryInterface::class, GenresRepository::class);
        $this->app->bind(ResponseDecoderInterface::class, JsonResponseDecoder::class);
        $this->app->bind(HttpServiceInterface::class, function () {
            return new HttpService(new Client(), $this->app->make(ResponseDecoderInterface::class));
        });
    }
}
