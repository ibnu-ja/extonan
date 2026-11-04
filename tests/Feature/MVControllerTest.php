<?php

namespace Tests\Feature;

use App\Enums\Permission;
use App\Enums\ResourceType;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Tests\TestCase;

class MVControllerTest extends TestCase
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

    private function createMv(array $overrides = []): Post
    {
        return Post::factory()->create(array_merge([
            'metadata' => ['post_type' => 'mv'],
            'is_published' => true,
            'is_current' => true,
            'postable_type' => null,
            'postable_id' => null,
        ], $overrides));
    }

    private function mvData(array $overrides = []): array
    {
        return array_merge([
            'title' => ['en' => fake()->sentence(3), 'native' => fake()->words(2, true)],
            'description' => ['en' => fake()->paragraph()],
            'postType' => 'mv',
            'isPublished' => false,
            'links' => [
                [
                    'name' => '1080p x265',
                    'type' => ResourceType::Link->value,
                    'value' => [
                        ['name' => 'Gudang Mirror', 'value' => fake()->url()],
                    ],
                ],
            ],
            'saluran' => [
                'name' => 'saluran',
                'type' => ResourceType::Saluran->value,
                'value' => [
                    ['name' => 'Primary HLS', 'value' => fake()->url().'/master.m3u8'],
                ],
            ],
            'embed' => [
                'name' => 'embed',
                'type' => ResourceType::Embed->value,
                'value' => [
                    ['name' => 'YouTube', 'value' => 'https://youtube.com/embed/'.fake()->slug()],
                ],
            ],
            'thumbnailItem' => null,
        ], $overrides);
    }

    public function test_guest_can_view_mv_index(): void
    {
        $publishedMv = $this->createMv(['title' => ['en' => fake()->sentence(3)]]);
        $this->createMv(['title' => ['en' => fake()->sentence(3)], 'is_published' => false]);
        Post::factory()->create([
            'title' => ['en' => fake()->sentence(3)],
            'metadata' => ['post_type' => 'tv', 'ep_no' => '1'],
            'is_published' => true,
            'is_current' => true,
        ]);

        $this->get(route('mv.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('mv/Index')
                ->has('mv.data', 1)
                ->where('mv.data.0.id', $publishedMv->id)
            );
    }

    public function test_mv_index_per_page_all(): void
    {
        Post::factory()->count(3)->create([
            'metadata' => ['post_type' => 'mv'],
            'is_published' => true,
            'is_current' => true,
            'postable_type' => null,
            'postable_id' => null,
        ]);

        $this->get(route('mv.index', ['perPage' => -1]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('mv/Index')
                ->has('mv.data', 3)
            );
    }

    public function test_guest_can_view_published_mv(): void
    {
        $mv = $this->createMv();

        $this->get(route('mv.show', $mv))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('mv/Show')
                ->where('post.id', $mv->id)
                ->has('related')
            );
    }

    public function test_mv_show_provides_related_posts_excluding_self(): void
    {
        Post::factory()->count(3)->create([
            'metadata' => ['post_type' => 'mv'],
            'is_published' => true,
            'is_current' => true,
            'postable_type' => null,
            'postable_id' => null,
        ]);

        $currentMv = $this->createMv();

        $this->get(route('mv.show', $currentMv))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('mv/Show')
                ->where('post.id', $currentMv->id)
                ->has('related', 3)
                ->where('related.0.id', fn ($id) => $id !== $currentMv->id)
                ->where('related.1.id', fn ($id) => $id !== $currentMv->id)
                ->where('related.2.id', fn ($id) => $id !== $currentMv->id)
            );
    }

    public function test_guest_cannot_view_unpublished_mv(): void
    {
        $mv = $this->createMv(['is_published' => false]);

        $this->get(route('mv.show', $mv))->assertForbidden();
    }

    public function test_guest_cannot_access_mv_create(): void
    {
        $this->get(route('mv.create'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_accessing_mv_create_redirects_to_shinrai_create(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_CREATE->value);

        $this->actingAs($user)
            ->get(route('mv.create'))
            ->assertRedirect(route('shinrai.create', ['type' => 'mv']));
    }

    public function test_guest_cannot_access_shinrai_create(): void
    {
        $this->get(route('shinrai.create'))->assertRedirect(route('login'));
    }

    public function test_user_without_permission_cannot_access_shinrai_create(): void
    {
        $this->setUpPermissions();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('shinrai.create'))
            ->assertForbidden();
    }

    public function test_user_with_permission_can_access_shinrai_create(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_CREATE->value);

        $this->actingAs($user)
            ->get(route('shinrai.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('shinrai/Create')
                ->where('type', 'mv')
            );
    }

    public function test_user_can_store_mv_post_as_draft_with_streams_and_links(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_CREATE->value);
        $data = $this->mvData();

        $response = $this->actingAs($user)->post(route('shinrai.store'), $data);

        $this->assertDatabaseHas('posts', [
            'author_id' => $user->id,
            'is_published' => false,
        ]);

        $post = Post::where('author_id', $user->id)->first();
        $this->assertNotNull($post);
        $response->assertRedirect(route('mv.show', $post));

        $this->assertCount(1, $post->links);
        $this->assertNotNull($post->saluran);
        $this->assertNotNull($post->embed);
    }

    public function test_user_without_publish_permission_cannot_store_published_mv(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_CREATE->value);
        $data = $this->mvData(['isPublished' => true]);

        $this->actingAs($user)
            ->post(route('shinrai.store'), $data)
            ->assertForbidden();
    }

    public function test_user_with_publish_permission_can_store_published_mv(): void
    {
        $user = $this->createUserWithPermission(
            Permission::POST_CREATE->value,
            Permission::POST_PUBLISH_ANY->value
        );

        $data = $this->mvData(['isPublished' => true]);

        $response = $this->actingAs($user)->post(route('shinrai.store'), $data);

        $post = Post::where('author_id', $user->id)->first();
        $this->assertNotNull($post);
        $response->assertRedirect(route('mv.show', $post));

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'author_id' => $user->id,
            'is_published' => true,
        ]);
    }

    public function test_user_with_update_permission_can_access_shinrai_edit(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_UPDATE_ANY->value);
        $mv = $this->createMv();

        $this->actingAs($user)
            ->get(route('shinrai.edit', $mv))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('shinrai/Create')
                ->where('post.id', $mv->id)
                ->where('type', 'mv')
            );
    }

    public function test_user_can_update_mv_post(): void
    {
        $user = $this->createUserWithPermission(
            Permission::POST_UPDATE_ANY->value,
            Permission::POST_PUBLISH_ANY->value
        );

        $mv = $this->createMv(['author_id' => $user->id]);

        $updateData = $this->mvData([
            'title' => ['en' => 'Updated MV Title'],
            'isPublished' => true,
        ]);

        $this->actingAs($user)
            ->put(route('shinrai.update', $mv), $updateData)
            ->assertRedirect(route('mv.show', $mv));

        $this->assertDatabaseHas('posts', [
            'id' => $mv->id,
            'title->en' => 'Updated MV Title',
        ]);
    }

    public function test_user_can_delete_mv_post(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_DELETE_ANY->value);
        $mv = $this->createMv(['author_id' => $user->id]);

        $this->actingAs($user)
            ->delete(route('shinrai.destroy', $mv))
            ->assertRedirect(route('mv.index'));

        $this->assertDatabaseMissing('posts', [
            'id' => $mv->id,
        ]);
    }
}
