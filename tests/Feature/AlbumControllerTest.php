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

class AlbumControllerTest extends TestCase
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

    private function createAlbum(array $overrides = []): Post
    {
        return Post::factory()->create(array_merge([
            'metadata' => ['post_type' => 'album'],
            'is_published' => true,
            'is_current' => true,
            'postable_type' => null,
            'postable_id' => null,
        ], $overrides));
    }

    private function createSingle(array $overrides = []): Post
    {
        return Post::factory()->create(array_merge([
            'metadata' => ['post_type' => 'single'],
            'is_published' => true,
            'is_current' => true,
            'postable_type' => null,
            'postable_id' => null,
        ], $overrides));
    }

    private function albumData(array $overrides = []): array
    {
        return array_merge([
            'title' => ['en' => fake()->sentence(3), 'native' => fake()->words(2, true)],
            'description' => ['en' => fake()->paragraph()],
            'postType' => 'album',
            'isPublished' => false,
            'links' => [
                [
                    'name' => 'FLAC 24bit',
                    'type' => ResourceType::Link->value,
                    'value' => [
                        ['name' => 'Gudang Mirror', 'value' => fake()->url()],
                    ],
                ],
            ],
            'embed' => null,
            'saluran' => null,
            'thumbnailItem' => null,
            'vgmdbData' => null,
        ], $overrides);
    }

    public function test_guest_can_view_published_albums_and_singles_index(): void
    {
        $this->createAlbum(['published_at' => now()->subDay()]);
        $this->createSingle(['published_at' => now()->subHours(2)]);
        $this->createAlbum([
            'is_published' => false,
            'published_at' => null,
        ]);

        $response = $this->get(route('album.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('album/Index')
            ->has('albums.data', 2)
            ->where('canCreate', false)
            ->where('canViewUnpublished', false)
        );
    }

    public function test_index_can_filter_by_type(): void
    {
        $this->createAlbum(['published_at' => now()->subDay()]);
        $this->createAlbum(['published_at' => now()->subDays(2)]);
        $this->createSingle(['published_at' => now()->subHours(2)]);

        $responseAlbums = $this->get(route('album.index', ['filter' => ['type' => 'album']]));
        $responseAlbums->assertOk();
        $responseAlbums->assertInertia(fn (Assert $page) => $page
            ->component('album/Index')
            ->has('albums.data', 2)
        );

        $responseSingles = $this->get(route('album.index', ['filter' => ['type' => 'single']]));
        $responseSingles->assertOk();
        $responseSingles->assertInertia(fn (Assert $page) => $page
            ->component('album/Index')
            ->has('albums.data', 1)
        );
    }

    public function test_index_can_search_by_title(): void
    {
        $this->createAlbum([
            'title' => ['en' => 'K-ON! Houkago Tea Time Album'],
            'published_at' => now()->subDay(),
        ]);
        $this->createSingle([
            'title' => ['en' => 'Bocchi the Rock Guitar Hero Single'],
            'published_at' => now()->subHours(2),
        ]);

        $response = $this->get(route('album.index', ['filter' => ['title' => 'Houkago']]));
        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('album/Index')
            ->has('albums.data', 1)
            ->where('albums.data.0.title.en', 'K-ON! Houkago Tea Time Album')
        );
    }

    public function test_index_can_paginate_with_all_items(): void
    {
        $this->createAlbum();
        $this->createSingle();

        $response = $this->get(route('album.index', ['perPage' => -1]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('album/Index')
            ->has('albums.data', 2)
        );
    }

    public function test_guest_can_view_published_album_show(): void
    {
        $album = $this->createAlbum();

        $response = $this->get(route('album.show', $album));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('album/Show')
            ->has('post')
            ->where('post.id', $album->id)
            ->where('canPublish', false)
        );
    }

    public function test_guest_can_view_published_single_show(): void
    {
        $single = $this->createSingle();

        $response = $this->get(route('album.show', $single));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('album/Show')
            ->has('post')
            ->where('post.id', $single->id)
            ->where('canPublish', false)
        );
    }

    public function test_guest_cannot_view_unpublished_album(): void
    {
        $album = $this->createAlbum([
            'is_published' => false,
            'published_at' => null,
        ]);

        $response = $this->get(route('album.show', $album));

        $response->assertForbidden();
    }

    public function test_user_with_view_any_can_view_unpublished_album(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_READ_ANY->value);
        $album = $this->createAlbum([
            'is_published' => false,
            'published_at' => null,
        ]);

        $response = $this->actingAs($user)->get(route('album.show', $album));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('album/Show')
            ->has('post')
            ->where('post.id', $album->id)
        );
    }

    public function test_create_redirects_to_shinrai_create(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_CREATE->value);

        $responseAlbum = $this->actingAs($user)->get(route('album.create'));
        $responseAlbum->assertRedirect(route('shinrai.create', ['type' => 'album']));

        $responseSingle = $this->actingAs($user)->get(route('album.create', ['type' => 'single']));
        $responseSingle->assertRedirect(route('shinrai.create', ['type' => 'single']));
    }

    public function test_unauthorized_user_cannot_access_edit(): void
    {
        $album = $this->createAlbum();

        $response = $this->get(route('album.edit', $album));
        $response->assertRedirect(route('login'));

        $regularUser = User::factory()->create();
        $responseAuth = $this->actingAs($regularUser)->get(route('album.edit', $album));
        $responseAuth->assertForbidden();
    }

    public function test_authorized_user_can_access_edit_and_redirects_to_shinrai(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_UPDATE_ANY->value);
        $album = $this->createAlbum();

        $response = $this->actingAs($user)->get(route('album.edit', $album));

        $response->assertRedirect(route('shinrai.edit', $album));
    }

    public function test_user_with_post_create_can_store_album_and_single_via_shinrai(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_CREATE->value);

        $albumPayload = $this->albumData(['postType' => 'album']);
        $responseAlbum = $this->actingAs($user)->post(route('shinrai.store'), $albumPayload);
        $albumPost = Post::where('metadata->post_type', 'album')->latest('id')->first();
        $this->assertNotNull($albumPost);
        $responseAlbum->assertRedirect(route('album.show', $albumPost));

        $singlePayload = $this->albumData(['postType' => 'single']);
        $responseSingle = $this->actingAs($user)->post(route('shinrai.store'), $singlePayload);
        $singlePost = Post::where('metadata->post_type', 'single')->latest('id')->first();
        $this->assertNotNull($singlePost);
        $responseSingle->assertRedirect(route('album.show', $singlePost));
    }

    public function test_user_with_post_update_can_update_album_via_shinrai(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_UPDATE_ANY->value);
        $album = $this->createAlbum();

        $updatePayload = $this->albumData([
            'title' => ['en' => 'Updated Album Title'],
            'postType' => 'album',
            'isPublished' => true,
        ]);

        $response = $this->actingAs($user)->put(route('shinrai.update', $album), $updatePayload);

        $response->assertRedirect(route('album.show', $album));
        $album->refresh();
        $this->assertSame('Updated Album Title', $album->getTranslation('title', 'en'));
    }

    public function test_user_with_post_delete_can_destroy_album_via_shinrai(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_DELETE_ANY->value);
        $album = $this->createAlbum();

        $response = $this->actingAs($user)->delete(route('shinrai.destroy', $album));

        $response->assertRedirect(route('album.index'));
        $this->assertDatabaseMissing('posts', ['id' => $album->id]);
    }

    public function test_shinrai_edit_supplies_vgmdb_data_to_form(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_UPDATE_ANY->value);
        $vgmdb = [
            'catalog' => 'TEST-001',
            'discs' => [
                [
                    'name' => 'Disc 1',
                    'disc_length' => '10:00',
                    'tracks' => [
                        [
                            'names' => ['English' => 'Song One', 'Romaji' => 'Uta Ichi'],
                            'track_length' => '4:20',
                        ],
                    ],
                ],
            ],
        ];

        $album = $this->createAlbum([
            'metadata' => [
                'post_type' => 'album',
                'vgmdb_data' => $vgmdb,
            ],
        ]);

        $response = $this->actingAs($user)->get(route('shinrai.edit', $album));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('shinrai/Create')
            ->has('post.vgmdbData')
            ->where('post.vgmdbData.catalog', 'TEST-001')
            ->where('post.vgmdbData.discs.0.tracks.0.names.English', 'Song One')
        );
    }

    public function test_user_can_create_and_update_album_with_tracklist(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_CREATE->value);
        $vgmdb = [
            'catalog' => 'ALBUM-100',
            'artist' => 'Test Artist',
            'discs' => [
                [
                    'name' => 'Disc 1 (CD)',
                    'disc_length' => '8:00',
                    'tracks' => [
                        [
                            'names' => ['English' => 'Track 1', 'Romaji' => 'Torakku 1'],
                            'track_length' => '3:30',
                        ],
                        [
                            'names' => ['English' => 'Track 2'],
                            'track_length' => '4:30',
                        ],
                    ],
                ],
            ],
        ];

        $payload = $this->albumData([
            'title' => ['en' => 'Album with Tracks'],
            'vgmdbData' => $vgmdb,
        ]);

        $response = $this->actingAs($user)->post(route('shinrai.store'), $payload);
        $album = Post::where('metadata->post_type', 'album')->latest('id')->first();
        $this->assertNotNull($album);
        $response->assertRedirect(route('album.show', $album));

        $this->assertSame('ALBUM-100', data_get($album->metadata, 'vgmdb_data.catalog'));
        $this->assertCount(2, data_get($album->metadata, 'vgmdb_data.discs.0.tracks'));
    }
}
