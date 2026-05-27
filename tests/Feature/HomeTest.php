<?php

namespace Tests\Feature;

use App\Models\Anime;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_home_page_returns_200(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $anime = Anime::factory()->create();
        Post::factory()->create(['postable_id' => $anime->id]);

        $response = $this->get(route('home'));

        $response->assertOk();
    }
}
