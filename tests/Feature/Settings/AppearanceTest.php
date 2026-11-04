<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AppearanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_appearance_page_is_accessible_by_guest(): void
    {
        $response = $this->get(route('appearance.edit'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('settings/Appearance')
            );
    }

    public function test_appearance_page_is_accessible_by_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('appearance.edit'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('settings/Appearance')
            );
    }

    public function test_settings_root_redirects_guest_to_appearance_page(): void
    {
        $response = $this->get(route('settings'));

        $response->assertRedirect(route('appearance.edit'));
    }

    public function test_settings_root_redirects_authenticated_user_to_profile_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('settings'));

        $response->assertRedirect(route('profile.edit'));
    }
}
