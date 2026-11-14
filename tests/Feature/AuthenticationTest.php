<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use RuntimeException;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_available_and_dashboard_is_protected(): void
    {
        $this->get('/login')->assertOk()->assertSee('Iniciar sesión');
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->create(['password' => 'test-password']);
        $this->post('/login', ['email' => $user->email, 'password' => 'test-password'])
            ->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
        $this->get('/admin')
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Nexus Space Apps');
        $this->get('/login')->assertRedirect('/admin');
        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_invalid_credentials_are_rejected_and_rate_limited(): void
    {
        $user = User::factory()->create(['password' => 'test-password']);
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])
                ->assertSessionHasErrors('email');
        }
        $this->post('/login', ['email' => $user->email, 'password' => 'test-password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_page_offers_google_authentication(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Continuar con Google')
            ->assertSee(route('auth.google.redirect'));
    }

    public function test_google_redirect_uses_the_socialite_provider(): void
    {
        $this->configureGoogle();
        Socialite::fake('google');

        $this->get('/auth/google/redirect')
            ->assertRedirect('https://socialite.fake/google/authorize');
    }

    public function test_user_can_login_with_a_verified_google_account(): void
    {
        $this->configureGoogle();
        Socialite::fake('google', SocialiteUser::fake([
            'id' => 'google-user-123',
            'name' => 'Google User',
            'email' => 'google@example.com',
            'email_verified' => true,
        ]));

        $this->googleCallback()->assertRedirect('/admin');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'Google User',
            'email' => 'google@example.com',
            'google_id' => 'google-user-123',
        ]);
    }

    public function test_google_login_rejects_an_unverified_email(): void
    {
        $this->configureGoogle();
        Socialite::fake('google', SocialiteUser::fake([
            'id' => 'google-user-456',
            'email' => 'unverified@example.com',
            'email_verified' => false,
        ]));

        $this->googleCallback()
            ->assertRedirect('/login')
            ->assertSessionHasErrors('google');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'unverified@example.com']);
    }

    public function test_google_login_displays_safe_oauth_diagnostics(): void
    {
        $this->configureGoogle();
        Socialite::fake('google', function (): never {
            throw new RuntimeException(
                'invalid_client client_secret=test-client-secret&code=sensitive-code'
            );
        });

        $this->googleCallback()
            ->assertRedirect('/login')
            ->assertSessionHasErrors([
                'google' => 'Google OAuth falló (RuntimeException): invalid_client client_secret=[REDACTED]&code=[REDACTED]',
            ]);
    }

    public function test_google_login_explains_a_cancelled_authorization(): void
    {
        $this->configureGoogle();

        $this->get('/auth/google/callback?'.http_build_query([
            'error' => 'access_denied',
            'error_description' => 'The user denied access',
        ]))
            ->assertRedirect('/login')
            ->assertSessionHasErrors([
                'google' => 'Google rechazó la autorización: access_denied — The user denied access',
            ]);
    }

    public function test_google_login_explains_a_callback_without_code(): void
    {
        $this->configureGoogle();

        $this->get('/auth/google/callback?scope=openid')
            ->assertRedirect('/login')
            ->assertSessionHasErrors([
                'google' => 'Google regresó sin código de autorización. Parámetros recibidos: scope.',
            ]);
    }

    private function configureGoogle(): void
    {
        config([
            'services.google.client_id' => 'test-client-id',
            'services.google.client_secret' => 'test-client-secret',
            'services.google.redirect' => '/auth/google/callback',
        ]);
    }

    private function googleCallback()
    {
        return $this->get('/auth/google/callback?code=test-authorization-code');
    }
}
