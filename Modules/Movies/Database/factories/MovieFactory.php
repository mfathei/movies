<?php
namespace Modules\Movies\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MovieFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Movies\Entities\Movie::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'adult' => $this->faker->boolean(),
            'backdrop_path' => $this->faker->filePath(),
            'homepage' => $this->faker->url(),
            'imdb_id' => $this->faker->randomDigit(),
            'original_language' => $this->faker->randomElement(['en', 'ar', 'ta','is']),
            'original_title' => $this->faker->sentence(3),
            'overview' => $this->faker->sentence(),
            'popularity' => $this->faker->randomFloat(2, 1, 100),
            'poster_path' => $this->faker->filePath(),
            'budget' => $this->faker->randomDigit(),
            'release_date' => $this->faker->date(),
            'revenue' => $this->faker->randomDigit(),
            'runtime' => $this->faker->randomDigit(),
            'status' => $this->faker->randomElement(['Released', 'Produced']),
            'tagline' => $this->faker->sentence(2),
            'title' => $this->faker->sentence(3),
            'video' => $this->faker->boolean(),
            'vote_average' => $this->faker->randomFloat(2, 1, 100),
            'vote_count' => $this->faker->randomDigit(),
        ];
    }
}

