<?php

namespace Tests\Feature;

use App\Enums\Permission;
use App\Models\Anime;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Tests\TestCase;

class AnimeControllerTest extends TestCase
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

    private function onePieceData(array $overrides = []): array
    {
        return array_merge([
            'title' => ['romaji' => 'ONE PIECE', 'native' => 'ONE PIECE', 'en' => 'ONE PIECE', 'id' => null],
            'description' => ['en' => 'Gold Roger was known as the Pirate King.', 'id' => null],
            'anilistId' => 21,
            'metadata' => [
                'id' => 21,
                'idMal' => 21,
                'episodes' => null,
                'coverImage' => [
                    'extraLarge' => 'https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx21-ELSYx3yMPcKM.jpg',
                    'large' => 'https://s4.anilist.co/file/anilistcdn/media/anime/cover/medium/bx21-ELSYx3yMPcKM.jpg',
                    'medium' => 'https://s4.anilist.co/file/anilistcdn/media/anime/cover/small/bx21-ELSYx3yMPcKM.jpg',
                    'color' => '#e49335',
                ],
                'title' => ['romaji' => 'ONE PIECE', 'english' => 'ONE PIECE', 'native' => 'ONE PIECE'],
                'startDate' => ['year' => 1999, 'month' => 10, 'day' => 20],
                'endDate' => ['year' => null, 'month' => null, 'day' => null],
                'episodes' => null,
                'description' => 'Gold Roger was known as the Pirate King.',
                'bannerImage' => 'https://s4.anilist.co/file/anilistcdn/media/anime/banner/21-wf37VakJmZqs.jpg',
                'season' => 'FALL',
                'seasonYear' => 1999,
                'seasonInt' => null,
                'genres' => ['Action', 'Adventure', 'Comedy', 'Drama', 'Fantasy'],
                'tags' => [
                    ['id' => 201, 'name' => 'Pirates', 'rank' => 98, 'isAdult' => false, 'category' => 'Cast-Traits', 'description' => 'Prominently features sea-faring adventurers.', 'isGeneralSpoiler' => false, 'isMediaSpoiler' => false],
                ],
                'studios' => ['edges' => [['node' => ['id' => 18, 'name' => 'Toei Animation'], 'isMain' => true]]],
                'characters' => ['edges' => []],
            ],
            'isPublished' => false,
        ], $overrides);
    }

    private function hxhData(array $overrides = []): array
    {
        return array_merge([
            'title' => ['romaji' => 'Hunter x Hunter (2011)', 'native' => 'HUNTER×HUNTER', 'en' => 'Hunter x Hunter', 'id' => null],
            'description' => ['en' => 'Gon Freecss aspires to become a Hunter.', 'id' => null],
            'anilistId' => 11061,
            'metadata' => [
                'id' => 11061,
                'idMal' => 11061,
                'episodes' => 148,
                'coverImage' => [
                    'extraLarge' => 'https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx11061-7pItrYGQJBmn.jpg',
                    'large' => 'https://s4.anilist.co/file/anilistcdn/media/anime/cover/medium/bx11061-7pItrYGQJBmn.jpg',
                    'medium' => 'https://s4.anilist.co/file/anilistcdn/media/anime/cover/small/bx11061-7pItrYGQJBmn.jpg',
                    'color' => '#16a040',
                ],
                'title' => ['romaji' => 'Hunter x Hunter (2011)', 'english' => 'Hunter x Hunter', 'native' => 'HUNTER×HUNTER'],
                'startDate' => ['year' => 2011, 'month' => 10, 'day' => 2],
                'endDate' => ['year' => 2014, 'month' => 9, 'day' => 24],
                'description' => 'Gon Freecss aspires to become a Hunter.',
                'bannerImage' => null,
                'season' => 'FALL',
                'seasonYear' => 2011,
                'seasonInt' => null,
                'genres' => ['Action', 'Adventure', 'Fantasy'],
                'tags' => [],
                'studios' => ['edges' => [['node' => ['id' => 11, 'name' => 'Madhouse'], 'isMain' => true]]],
                'characters' => ['edges' => []],
            ],
            'isPublished' => false,
        ], $overrides);
    }

    // ─── store() ───────────────────────────────────────────────

    public function test_guest_cannot_store_anime(): void
    {
        $response = $this->post(route('anime.store'), $this->onePieceData());

        $response->assertRedirect(route('login'));
    }

    public function test_user_without_create_permission_cannot_store_anime(): void
    {
        $user = User::factory()->create();
        $this->setUpPermissions();

        $response = $this->actingAs($user)->post(route('anime.store'), $this->onePieceData());

        $response->assertForbidden();
    }

    public function test_user_can_store_anime_as_draft(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_CREATE->value);

        $response = $this->actingAs($user)->post(route('anime.store'), $this->onePieceData());

        $response->assertRedirect(route('anime.index'));
        $this->assertDatabaseHas('anime', [
            'title->romaji' => 'ONE PIECE',
            'title->native' => 'ONE PIECE',
            'anilist_id' => 21,
            'is_published' => false,
            'author_id' => $user->id,
        ]);
    }

    public function test_user_can_store_anime_as_published(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_CREATE->value, Permission::POST_PUBLISH_SELF->value);

        $response = $this->actingAs($user)->post(route('anime.store'), $this->onePieceData([
            'isPublished' => true,
        ]));

        $response->assertRedirect(route('anime.index'));
        $this->assertDatabaseHas('anime', [
            'title->romaji' => 'ONE PIECE',
            'anilist_id' => 21,
            'is_published' => true,
            'author_id' => $user->id,
        ]);
    }

    public function test_user_without_publish_permission_cannot_publish_anime(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_CREATE->value);

        $response = $this->actingAs($user)->post(route('anime.store'), $this->onePieceData([
            'isPublished' => true,
        ]));

        $response->assertForbidden();
    }

    public function test_store_validates_required_fields(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_CREATE->value);

        $response = $this->actingAs($user)->post(route('anime.store'), [
            'title' => ['romaji' => null, 'native' => null],
            'description' => null,
            'anilistId' => null,
            'isPublished' => null,
        ]);

        $response->assertSessionHasErrors(['title.romaji', 'title.native', 'description', 'anilistId', 'isPublished']);
    }

    public function test_store_sets_author_id_from_authenticated_user(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_CREATE->value);

        $this->actingAs($user)->post(route('anime.store'), $this->onePieceData());

        $anime = Anime::where('title->romaji', 'ONE PIECE')->first();
        $this->assertNotNull($anime);
        $this->assertEquals($user->id, $anime->author_id);
    }

    // ─── edit() ────────────────────────────────────────────────

    public function test_guest_cannot_edit_anime(): void
    {
        $author = User::factory()->create();
        $anime = Anime::factory()->create(['author_id' => $author->id]);

        $response = $this->get(route('anime.edit', $anime));

        $response->assertRedirect(route('login'));
    }

    public function test_user_without_update_permission_cannot_edit_anime(): void
    {
        $user = User::factory()->create();
        $this->setUpPermissions();
        $author = User::factory()->create();
        $anime = Anime::factory()->create(['author_id' => $author->id]);

        $response = $this->actingAs($user)->get(route('anime.edit', $anime));

        $response->assertForbidden();
    }

    public function test_user_with_update_self_can_edit_own_anime(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_UPDATE_SELF->value);
        $anime = Anime::factory()->create(['author_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('anime.edit', $anime));

        $response->assertOk();
    }

    public function test_user_with_update_any_can_edit_any_anime(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_UPDATE_ANY->value);
        $author = User::factory()->create();
        $anime = Anime::factory()->create(['author_id' => $author->id]);

        $response = $this->actingAs($user)->get(route('anime.edit', $anime));

        $response->assertOk();
    }

    // ─── update() ──────────────────────────────────────────────

    public function test_guest_cannot_update_anime(): void
    {
        $author = User::factory()->create();
        $anime = Anime::factory()->create(['author_id' => $author->id]);

        $response = $this->put(route('anime.update', $anime), $this->onePieceData());

        $response->assertRedirect(route('login'));
    }

    public function test_user_without_update_permission_cannot_update_anime(): void
    {
        $user = User::factory()->create();
        $this->setUpPermissions();
        $author = User::factory()->create();
        $anime = Anime::factory()->create(['author_id' => $author->id]);

        $response = $this->actingAs($user)->put(route('anime.update', $anime), $this->onePieceData());

        $response->assertForbidden();
    }

    public function test_user_with_update_self_can_update_own_anime(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_UPDATE_SELF->value);
        $anime = Anime::factory()->create(['author_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('anime.update', $anime), $this->hxhData());

        $response->assertRedirect(route('anime.show', $anime));
        $this->assertDatabaseHas('anime', [
            'id' => $anime->id,
            'title->romaji' => 'Hunter x Hunter (2011)',
            'anilist_id' => 11061,
        ]);
    }

    public function test_user_with_update_any_can_update_any_anime(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_UPDATE_ANY->value);
        $author = User::factory()->create();
        $anime = Anime::factory()->create(['author_id' => $author->id]);

        $response = $this->actingAs($user)->put(route('anime.update', $anime), $this->hxhData());

        $response->assertRedirect(route('anime.show', $anime));
        $this->assertDatabaseHas('anime', [
            'id' => $anime->id,
            'title->romaji' => 'Hunter x Hunter (2011)',
        ]);
    }

    public function test_user_with_update_self_cannot_update_other_anime(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_UPDATE_SELF->value);
        $otherUser = User::factory()->create();
        $anime = Anime::factory()->create(['author_id' => $otherUser->id]);

        $response = $this->actingAs($user)->put(route('anime.update', $anime), $this->onePieceData());

        $response->assertForbidden();
    }

    public function test_user_without_publish_permission_cannot_publish_on_update(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_UPDATE_SELF->value);
        $anime = Anime::factory()->create(['author_id' => $user->id, 'is_published' => false]);

        $response = $this->actingAs($user)->put(route('anime.update', $anime), $this->onePieceData([
            'isPublished' => true,
        ]));

        $response->assertForbidden();
    }

    public function test_update_replaces_metadata(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_UPDATE_SELF->value);
        $anime = Anime::factory()->create([
            'author_id' => $user->id,
            'metadata' => ['genres' => ['Action'], 'bannerImage' => 'old.jpg'],
        ]);

        $response = $this->actingAs($user)->put(route('anime.update', $anime), $this->hxhData());

        $response->assertRedirect(route('anime.show', $anime));
        $anime->refresh();
        $this->assertEquals(['Action', 'Adventure', 'Fantasy'], $anime->metadata->genres);
        $this->assertNull($anime->metadata->bannerImage ?? null);
    }

    public function test_update_validates_required_fields(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_UPDATE_SELF->value);
        $anime = Anime::factory()->create(['author_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('anime.update', $anime), [
            'title' => ['romaji' => null, 'native' => null],
            'description' => null,
            'anilistId' => null,
            'isPublished' => null,
        ]);

        $response->assertSessionHasErrors(['title.romaji', 'title.native', 'description', 'anilistId', 'isPublished']);
    }

    // ─── delete() ──────────────────────────────────────────────

    public function test_guest_cannot_delete_anime(): void
    {
        $author = User::factory()->create();
        $anime = Anime::factory()->create(['author_id' => $author->id]);

        $response = $this->delete(route('anime.destroy', $anime));

        $response->assertRedirect(route('login'));
    }

    public function test_user_without_delete_permission_cannot_delete_anime(): void
    {
        $user = User::factory()->create();
        $this->setUpPermissions();
        $author = User::factory()->create();
        $anime = Anime::factory()->create(['author_id' => $author->id]);

        $response = $this->actingAs($user)->delete(route('anime.destroy', $anime));

        $response->assertForbidden();
    }

    public function test_user_with_delete_self_can_delete_own_anime(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_DELETE_SELF->value);
        $anime = Anime::factory()->create(['author_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('anime.destroy', $anime));

        $response->assertRedirect(route('anime.index'));
        $this->assertDatabaseMissing('anime', ['id' => $anime->id]);
    }

    public function test_user_with_delete_any_can_delete_any_anime(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_DELETE_ANY->value);
        $author = User::factory()->create();
        $anime = Anime::factory()->create(['author_id' => $author->id]);

        $response = $this->actingAs($user)->delete(route('anime.destroy', $anime));

        $response->assertRedirect(route('anime.index'));
        $this->assertDatabaseMissing('anime', ['id' => $anime->id]);
    }

    public function test_user_with_delete_self_cannot_delete_other_anime(): void
    {
        $user = $this->createUserWithPermission(Permission::POST_DELETE_SELF->value);
        $otherUser = User::factory()->create();
        $anime = Anime::factory()->create(['author_id' => $otherUser->id]);

        $response = $this->actingAs($user)->delete(route('anime.destroy', $anime));

        $response->assertForbidden();
    }
}
