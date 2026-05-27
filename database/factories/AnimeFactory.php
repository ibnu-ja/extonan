<?php

namespace Database\Factories;

use App\Models\Anime;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Anime>
 */
class AnimeFactory extends Factory
{
    protected $model = Anime::class;

    public function definition(): array
    {
        return [
            'title' => ['en' => fake()->sentence(3), 'native' => fake()->sentence(3)],
            'description' => ['en' => fake()->paragraph()],
            'anilist_id' => fake()->unique()->numberBetween(1, 99999),
            'author_id' => User::factory(),
            'metadata' => [
                'genres' => fake()->randomElements(['Action', 'Drama', 'Comedy', 'Romance', 'Fantasy'], 2),
                'bannerImage' => fake()->imageUrl(),
                'coverImage' => [
                    'extraLarge' => fake()->imageUrl(1200, 1800),
                    'large' => fake()->imageUrl(600, 900),
                    'medium' => fake()->imageUrl(300, 450),
                    'color' => fake()->hexColor(),
                ],
            ],
            'is_published' => true,
            'is_current' => true,
            'published_at' => now(),
        ];
    }
}
