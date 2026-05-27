<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Junges\InviteCodes\Models\Invite;
use Laravel\Fortify\Features;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->skipUnlessFortifyHas(Features::registration());
    }

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get(route('register'));

        $response->assertOk();
    }

    public function test_registration_fails_without_invite_code()
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('invite_code');
        $this->assertGuest();
    }

    public function test_registration_fails_with_invalid_invite_code()
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'invite_code' => 'NONEXISTENT',
        ]);

        $response->assertSessionHasErrors('invite_code');
        $this->assertGuest();
    }

    public function test_registration_fails_with_expired_invite_code()
    {
        Invite::create([
            'code' => 'EXPIRED123',
            'expires_at' => now()->subDay(),
            'max_usages' => 1,
        ]);

        $response = $this->post(route('register.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'invite_code' => 'EXPIRED123',
        ]);

        $response->assertSessionHasErrors('invite_code');
        $this->assertGuest();
    }

    public function test_registration_fails_with_sold_out_invite_code()
    {
        Invite::create([
            'code' => 'SOLDOUT123',
            'max_usages' => 1,
            'uses' => 1,
        ]);

        $response = $this->post(route('register.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'invite_code' => 'SOLDOUT123',
        ]);

        $response->assertSessionHasErrors('invite_code');
        $this->assertGuest();
    }

    public function test_new_users_can_register_with_valid_invite_code()
    {
        Invite::create([
            'code' => 'VALIDCODE',
            'max_usages' => 1,
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->post(route('register.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'invite_code' => 'VALIDCODE',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('invites', [
            'code' => 'VALIDCODE',
            'uses' => 1,
        ]);
    }
}
