<?php

namespace Tests\Feature;

use App\Enums\Permission;
use App\Models\Anime;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_home_page_returns_200(): void
    {
        $anime = Anime::factory()->create();
        Post::factory()->create(['postable_id' => $anime->id]);

        $this->get(route('home'))->assertOk();
    }

    public function test_home_page_does_not_show_drafts_for_guest(): void
    {
        $publishedAnime = Anime::factory()->create([
            'is_published' => true,
            'is_current' => true,
            'published_at' => now(),
        ]);
        Anime::factory()->create([
            'is_published' => false,
            'is_current' => true,
            'published_at' => null,
        ]);

        $publishedEpisode = Post::factory()->create([
            'postable_type' => Anime::class,
            'postable_id' => $publishedAnime->id,
            'is_published' => true,
            'is_current' => true,
            'published_at' => now(),
        ]);
        Post::factory()->create([
            'postable_type' => Anime::class,
            'postable_id' => $publishedAnime->id,
            'is_published' => false,
            'is_current' => true,
            'published_at' => null,
        ]);

        $publishedMv = Post::factory()->create([
            'metadata' => ['post_type' => 'mv'],
            'postable_type' => null,
            'postable_id' => null,
            'is_published' => true,
            'is_current' => true,
            'published_at' => now(),
        ]);
        Post::factory()->create([
            'metadata' => ['post_type' => 'mv'],
            'postable_type' => null,
            'postable_id' => null,
            'is_published' => false,
            'is_current' => true,
            'published_at' => null,
        ]);

        $publishedAlbum = Post::factory()->create([
            'metadata' => ['post_type' => 'album'],
            'postable_type' => null,
            'postable_id' => null,
            'is_published' => true,
            'is_current' => true,
            'published_at' => now(),
        ]);
        Post::factory()->create([
            'metadata' => ['post_type' => 'album'],
            'postable_type' => null,
            'postable_id' => null,
            'is_published' => false,
            'is_current' => true,
            'published_at' => null,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Home')
                ->has('latestAnime', 1)
                ->where('latestAnime.0.id', $publishedAnime->id)
                ->has('latestEpisodes', 1)
                ->where('latestEpisodes.0.id', $publishedEpisode->id)
                ->has('latestMv', 1)
                ->where('latestMv.0.id', $publishedMv->id)
                ->has('latestAlbum', 1)
                ->where('latestAlbum.0.id', $publishedAlbum->id)
            );
    }

    public function test_home_page_shows_drafts_for_editor(): void
    {
        SpatiePermission::findOrCreate(Permission::POST_READ_ANY->value);
        $editor = User::factory()->create();
        $editor->givePermissionTo(Permission::POST_READ_ANY->value);

        Anime::factory()->create([
            'is_published' => true,
            'is_current' => true,
            'published_at' => now(),
        ]);
        Anime::factory()->create([
            'is_published' => false,
            'is_current' => true,
            'published_at' => null,
        ]);

        Post::factory()->create([
            'metadata' => ['post_type' => 'mv'],
            'postable_type' => null,
            'postable_id' => null,
            'is_published' => true,
            'is_current' => true,
            'published_at' => now(),
        ]);
        Post::factory()->create([
            'metadata' => ['post_type' => 'mv'],
            'postable_type' => null,
            'postable_id' => null,
            'is_published' => false,
            'is_current' => true,
            'published_at' => null,
        ]);

        $this->actingAs($editor)
            ->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Home')
                ->has('latestAnime', 2)
                ->has('latestMv', 2)
            );
    }
}
