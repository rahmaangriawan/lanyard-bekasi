<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Support\SecureSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_legacy_kawruh_login_path_redirects_to_login(): void
    {
        $response = $this->get('/kawruh/login');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_are_not_sent_to_kawruh_login(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/kawruh/login');

        $response->assertRedirect('/dashboard');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_login_screen_renders_turnstile_when_enabled(): void
    {
        SecureSettings::put('turnstile_login_enabled', 'true');
        SecureSettings::put('turnstile_site_key', 'test-site-key');
        SecureSettings::put('turnstile_secret_key', 'test-secret-key');

        $this->get('/login')
            ->assertOk()
            ->assertSee('cf-turnstile', false)
            ->assertSee('test-site-key', false)
            ->assertSee('https://challenges.cloudflare.com/turnstile/v0/api.js', false)
            ->assertDontSee('test-secret-key', false);
    }

    public function test_login_is_rejected_when_turnstile_is_missing(): void
    {
        $user = User::factory()->create();
        SecureSettings::put('turnstile_login_enabled', 'true');
        SecureSettings::put('turnstile_site_key', 'test-site-key');
        SecureSettings::put('turnstile_secret_key', 'test-secret-key');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('cf-turnstile-response');

        $this->assertGuest();
    }

    public function test_user_can_login_after_turnstile_verification_succeeds(): void
    {
        Http::fake([
            'challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true]),
        ]);
        $user = User::factory()->create();
        SecureSettings::put('turnstile_login_enabled', 'true');
        SecureSettings::put('turnstile_site_key', 'test-site-key');
        SecureSettings::put('turnstile_secret_key', 'test-secret-key');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'cf-turnstile-response' => 'valid-token',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
        Http::assertSent(fn ($request) => $request['secret'] === 'test-secret-key'
            && $request['response'] === 'valid-token');
    }
}
