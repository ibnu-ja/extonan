<?php

namespace Database\Factories;

use App\Models\Anime;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title' => ['en' => fake()->sentence(4), 'native' => fake()->sentence(4)],
            'description' => ['en' => fake()->paragraph()],
            'author_id' => User::factory(),
            'postable_type' => Anime::class,
            'postable_id' => Anime::factory(),
            'metadata' => [
                'ep_no' => (string) fake()->numberBetween(1, 24),
                'post_type' => fake()->randomElement(['tv', 'bd', 'movie']),
            ],
            'is_published' => true,
            'is_current' => true,
            'published_at' => now(),
        ];
    }
}
