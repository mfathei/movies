<?php

namespace Modules\Movies\Tests\Feature;

use Modules\Movies\Entities\Genre;
use Modules\Movies\Entities\Movie;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MovieApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @group movies
     * @return void
     */
    public function test_list_movies_api(): void
    {
        // Arrange
        $genres = Genre::factory(3)->create();
        $movie = Movie::factory()->create();
        $movie->genres()->sync($genres->pluck('id'));

        // Act
        $response = $this->json('GET', route('movies.list'));

        // Assert
        $response->assertSuccessful();
        $response->assertJson([
            "data" => [
                [
                    "id" => 1,
                    "adult" => $movie->adult,
                    "backdrop_path" => $movie->backdrop_path,
                    "homepage" =>  $movie->homepage,
                    "imdb_id" => $movie->imdb_id,
                    "original_language" => $movie->original_language,
                    "original_title" => $movie->original_title,
                    "overview" => $movie->overview,
                    "popularity" => $movie->popularity,
                    "poster_path" => $movie->poster_path,
                    "budget" => $movie->budget,
                    "release_date" => $movie->release_date->format('Y-m-d'),
                    "revenue" => $movie->revenue,
                    "runtime" => $movie->runtime,
                    "status" => $movie->status,
                    "tagline" => $movie->tagline,
                    "title" => $movie->title,
                    "video" => $movie->video,
                    "vote_average" => $movie->vote_average,
                    "vote_count" => $movie->vote_count,
                    "genres" => [
                        ["id" => 1, "name" => $genres->first()->name],
                        ["id" => 2, "name" => $genres->get(1)->name],
                        ["id" => 3, "name" => $genres->get(2)->name],
                    ]
                ],
            ],
        ]);
    }
}
