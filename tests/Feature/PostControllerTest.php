<?php

namespace Tests\Feature;

use App\Enums\Permission;
use App\Models\Anime;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function setUpPermissions(): void
    {
        foreach (Permission::cases() as $permission) {
            SpatiePermission::findOrCreate($permission->value);
        }
    }

    private function createUserWithPermission(string ...$perms): User
    {
        $this->setUpPermissions();

        $user = User::factory()->create();
        $user->givePermissionTo($perms);

        return $user;
    }

    private function episodeData(array $overrides = []): array
    {
        return array_merge([
            'title' => ['en' => 'Episode 1: The Beginning', 'native' => '第1話 始まり'],
            'description' => ['en' => 'The story begins with our hero.'],
            'postType' => 'tv',
            'epNo' => '1',
            'isPublished' => false,
            'links' => null,
            'thumbnailItem' => null,
        ], $overrides);
    }

    public function test_guest_cannot_view_create_page(): void
    {
        $anime = Anime::factory()->create();

        $response = $this->get(route('post.create', $anime));

        $response->assertRedirect(route('login'));
    }

    public function test_user_without_create_permission_cannot_view_create_page(): void
    {
        $user = User::factory()->create();
        $this->setUpPermissions();
        $anime = Anime::factory()->create();

        $response = $this->actingAs($user)->get(route('post.create', $anime));

        $response->assertForbidden();
    }

    public function test_user_with_create_permission_can_view_create_page(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_CREATE->value);
        $anime = Anime::factory()->create();

        $response = $this->actingAs($user)->get(route('post.create', $anime));

        $response->assertOk();
    }

    public function test_guest_cannot_store_post(): void
    {
        $anime = Anime::factory()->create();

        $response = $this->post(route('post.store', $anime), $this->episodeData());

        $response->assertForbidden();
    }

    public function test_user_without_create_permission_cannot_store_post(): void
    {
        $user = User::factory()->create();
        $this->setUpPermissions();
        $anime = Anime::factory()->create();

        $response = $this->actingAs($user)->post(route('post.store', $anime), $this->episodeData());

        $response->assertForbidden();
    }

    public function test_user_can_store_post_as_draft(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_CREATE->value);
        $anime = Anime::factory()->create();

        $response = $this->actingAs($user)->post(route('post.store', $anime), $this->episodeData());

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', [
            'title->en' => 'Episode 1: The Beginning',
            'postable_id' => $anime->id,
            'postable_type' => Anime::class,
            'is_published' => false,
            'author_id' => $user->id,
        ]);
    }

    public function test_user_can_store_post_as_published(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_CREATE->value, Permission::POST_PUBLISH_SELF->value);
        $anime = Anime::factory()->create();

        $response = $this->actingAs($user)->post(route('post.store', $anime), $this->episodeData([
            'isPublished' => true,
        ]));

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', [
            'title->en' => 'Episode 1: The Beginning',
            'is_published' => true,
            'author_id' => $user->id,
        ]);
    }

    public function test_user_without_publish_permission_cannot_publish_on_store(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_CREATE->value);
        $anime = Anime::factory()->create();

        $response = $this->actingAs($user)->post(route('post.store', $anime), $this->episodeData([
            'isPublished' => true,
        ]));

        $response->assertForbidden();
    }

    public function test_store_validates_required_fields(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_CREATE->value);
        $anime = Anime::factory()->create();

        $response = $this->actingAs($user)->post(route('post.store', $anime), [
            'title' => null,
            'description' => null,
            'postType' => null,
            'isPublished' => null,
        ]);

        $response->assertSessionHasErrors(['title', 'description', 'postType']);
    }

    public function test_store_sets_author_id_from_authenticated_user(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_CREATE->value);
        $anime = Anime::factory()->create();

        $this->actingAs($user)->post(route('post.store', $anime), $this->episodeData());

        $post = Post::where('title->en', 'Episode 1: The Beginning')->first();
        $this->assertNotNull($post);
        $this->assertEquals($user->id, $post->author_id);
    }

    public function test_store_stores_metadata(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_CREATE->value);
        $anime = Anime::factory()->create();

        $this->actingAs($user)->post(route('post.store', $anime), $this->episodeData());

        $post = Post::where('title->en', 'Episode 1: The Beginning')->first();
        $this->assertNotNull($post);
        $this->assertEquals('tv', $post->metadata->post_type);
        $this->assertEquals('1', $post->metadata->ep_no);
    }

    public function test_store_creates_links_when_provided(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_CREATE->value);
        $anime = Anime::factory()->create();

        $this->actingAs($user)->post(route('post.store', $anime), $this->episodeData([
            'links' => [
                [
                    'name' => 'Stream',
                    'type' => 'link',
                    'value' => [
                        ['name' => 'url', 'value' => 'https://example.com/stream'],
                        ['name' => 'label', 'value' => 'HD'],
                    ],
                ],
            ],
        ]));

        $post = Post::where('title->en', 'Episode 1: The Beginning')->first();
        $this->assertNotNull($post);
        $this->assertCount(1, $post->resources);
        $this->assertEquals('Stream', $post->resources->first()->name);
        $this->assertEquals('link', $post->resources->first()->type);
    }

    public function test_guest_can_view_published_post(): void
    {
        $anime = Anime::factory()->create();
        $post = Post::factory()->create([
            'postable_id' => $anime->id,
            'postable_type' => Anime::class,
            'is_published' => true,
        ]);

        $response = $this->get(route('post.show', [$anime, $post]));

        $response->assertOk();
    }

    public function test_user_with_read_any_can_view_unpublished_post(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_READ_ANY->value);
        $anime = Anime::factory()->create();
        $post = Post::factory()->create([
            'postable_id' => $anime->id,
            'postable_type' => Anime::class,
            'is_published' => false,
        ]);

        $response = $this->actingAs($user)->get(route('post.show', [$anime, $post]));

        $response->assertOk();
    }

    public function test_user_with_read_self_can_view_own_unpublished_post(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_READ_SELF->value);
        $anime = Anime::factory()->create();
        $post = Post::factory()->create([
            'postable_id' => $anime->id,
            'postable_type' => Anime::class,
            'is_published' => false,
            'author_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('post.show', [$anime, $post]));

        $response->assertOk();
    }

    public function test_user_without_permission_cannot_view_unpublished_post(): void
    {
        $user = User::factory()->create();
        $this->setUpPermissions();
        $anime = Anime::factory()->create();
        $post = Post::factory()->create([
            'postable_id' => $anime->id,
            'postable_type' => Anime::class,
            'is_published' => false,
        ]);

        $response = $this->actingAs($user)->get(route('post.show', [$anime, $post]));

        $response->assertForbidden();
    }

    public function test_guest_cannot_view_edit_page(): void
    {
        $author = User::factory()->create();
        $anime = Anime::factory()->create();
        $post = Post::factory()->create([
            'postable_id' => $anime->id,
            'postable_type' => Anime::class,
            'author_id' => $author->id,
        ]);

        $response = $this->get(route('post.edit', [$anime, $post]));

        $response->assertRedirect(route('login'));
    }

    public function test_user_without_update_permission_cannot_edit_post(): void
    {
        $user = User::factory()->create();
        $this->setUpPermissions();
        $anime = Anime::factory()->create();
        $post = Post::factory()->create([
            'postable_id' => $anime->id,
            'postable_type' => Anime::class,
        ]);

        $response = $this->actingAs($user)->get(route('post.edit', [$anime, $post]));

        $response->assertForbidden();
    }

    public function test_user_with_update_self_can_edit_own_post(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_UPDATE_SELF->value);
        $anime = Anime::factory()->create();
        $post = Post::factory()->create([
            'postable_id' => $anime->id,
            'postable_type' => Anime::class,
            'author_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('post.edit', [$anime, $post]));

        $response->assertOk();
    }

    public function test_user_with_update_any_can_edit_any_post(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_UPDATE_ANY->value);
        $author = User::factory()->create();
        $anime = Anime::factory()->create();
        $post = Post::factory()->create([
            'postable_id' => $anime->id,
            'postable_type' => Anime::class,
            'author_id' => $author->id,
        ]);

        $response = $this->actingAs($user)->get(route('post.edit', [$anime, $post]));

        $response->assertOk();
    }

    public function test_user_with_update_self_cannot_edit_other_post(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_UPDATE_SELF->value);
        $otherUser = User::factory()->create();
        $anime = Anime::factory()->create();
        $post = Post::factory()->create([
            'postable_id' => $anime->id,
            'postable_type' => Anime::class,
            'author_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->get(route('post.edit', [$anime, $post]));

        $response->assertForbidden();
    }

    public function test_guest_cannot_update_post(): void
    {
        $anime = Anime::factory()->create();
        $post = Post::factory()->create([
            'postable_id' => $anime->id,
            'postable_type' => Anime::class,
        ]);

        $response = $this->put(route('post.update', [$anime, $post]), $this->episodeData());

        $response->assertRedirect(route('login'));
    }

    public function test_user_without_update_permission_cannot_update_post(): void
    {
        $user = User::factory()->create();
        $this->setUpPermissions();
        $anime = Anime::factory()->create();
        $post = Post::factory()->create([
            'postable_id' => $anime->id,
            'postable_type' => Anime::class,
        ]);

        $response = $this->actingAs($user)->put(route('post.update', [$anime, $post]), $this->episodeData());

        $response->assertForbidden();
    }

    public function test_user_with_update_self_can_update_own_post(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_UPDATE_SELF->value);
        $anime = Anime::factory()->create();
        $post = Post::factory()->create([
            'postable_id' => $anime->id,
            'postable_type' => Anime::class,
            'author_id' => $user->id,
            'title' => ['en' => 'Old Title', 'native' => '旧タイトル'],
        ]);

        $response = $this->actingAs($user)->put(route('post.update', [$anime, $post]), $this->episodeData());

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title->en' => 'Episode 1: The Beginning',
        ]);
    }

    public function test_user_with_update_any_can_update_any_post(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_UPDATE_ANY->value);
        $author = User::factory()->create();
        $anime = Anime::factory()->create();
        $post = Post::factory()->create([
            'postable_id' => $anime->id,
            'postable_type' => Anime::class,
            'author_id' => $author->id,
        ]);

        $response = $this->actingAs($user)->put(route('post.update', [$anime, $post]), $this->episodeData());

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title->en' => 'Episode 1: The Beginning',
        ]);
    }

    public function test_user_with_update_self_cannot_update_other_post(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_UPDATE_SELF->value);
        $otherUser = User::factory()->create();
        $anime = Anime::factory()->create();
        $post = Post::factory()->create([
            'postable_id' => $anime->id,
            'postable_type' => Anime::class,
            'author_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->put(route('post.update', [$anime, $post]), $this->episodeData());

        $response->assertForbidden();
    }

    public function test_user_without_publish_permission_cannot_publish_on_update(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_UPDATE_SELF->value);
        $anime = Anime::factory()->create();
        $post = Post::factory()->create([
            'postable_id' => $anime->id,
            'postable_type' => Anime::class,
            'author_id' => $user->id,
            'is_published' => false,
        ]);

        $response = $this->actingAs($user)->put(route('post.update', [$anime, $post]), $this->episodeData([
            'isPublished' => true,
        ]));

        $response->assertForbidden();
    }

    public function test_update_validates_required_fields(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_UPDATE_SELF->value);
        $anime = Anime::factory()->create();
        $post = Post::factory()->create([
            'postable_id' => $anime->id,
            'postable_type' => Anime::class,
            'author_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->put(route('post.update', [$anime, $post]), [
            'title' => null,
            'description' => null,
            'postType' => null,
            'isPublished' => null,
        ]);

        $response->assertSessionHasErrors(['title', 'description', 'postType']);
    }

    public function test_guest_cannot_destroy_post(): void
    {
        $anime = Anime::factory()->create();
        $post = Post::factory()->create([
            'postable_id' => $anime->id,
            'postable_type' => Anime::class,
        ]);

        $response = $this->delete(route('post.destroy', [$anime, $post]));

        $response->assertRedirect(route('login'));
    }

    public function test_user_without_delete_permission_cannot_destroy_post(): void
    {
        $user = User::factory()->create();
        $this->setUpPermissions();
        $anime = Anime::factory()->create();
        $post = Post::factory()->create([
            'postable_id' => $anime->id,
            'postable_type' => Anime::class,
        ]);

        $response = $this->actingAs($user)->delete(route('post.destroy', [$anime, $post]));

        $response->assertForbidden();
    }

    public function test_user_with_delete_self_can_destroy_own_post(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_DELETE_SELF->value);
        $anime = Anime::factory()->create();
        $post = Post::factory()->create([
            'postable_id' => $anime->id,
            'postable_type' => Anime::class,
            'author_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(route('post.destroy', [$anime, $post]));

        $response->assertRedirect(route('anime.show', $anime));
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_user_with_delete_any_can_destroy_any_post(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_DELETE_ANY->value);
        $author = User::factory()->create();
        $anime = Anime::factory()->create();
        $post = Post::factory()->create([
            'postable_id' => $anime->id,
            'postable_type' => Anime::class,
            'author_id' => $author->id,
        ]);

        $response = $this->actingAs($user)->delete(route('post.destroy', [$anime, $post]));

        $response->assertRedirect(route('anime.show', $anime));
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_user_with_delete_self_cannot_destroy_other_post(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_DELETE_SELF->value);
        $otherUser = User::factory()->create();
        $anime = Anime::factory()->create();
        $post = Post::factory()->create([
            'postable_id' => $anime->id,
            'postable_type' => Anime::class,
            'author_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->delete(route('post.destroy', [$anime, $post]));

        $response->assertForbidden();
    }
}
