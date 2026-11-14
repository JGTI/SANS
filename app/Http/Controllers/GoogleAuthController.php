<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        if (! $this->isConfigured()) {
            return $this->configurationError();
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        if (! $this->isConfigured()) {
            return $this->configurationError();
        }

        if ($request->filled('error')) {
            $error = Str::limit((string) $request->query('error'), 100);
            $description = Str::limit(
                trim((string) $request->query('error_description')),
                300
            );

            return redirect()->route('login')->withErrors([
                'google' => sprintf(
                    'Google rechazó la autorización: %s%s',
                    $error,
                    $description !== '' ? ' — '.$description : ''
                ),
            ]);
        }

        if (! $request->filled('code')) {
            $queryKeys = array_keys($request->query());

            return redirect()->route('login')->withErrors([
                'google' => sprintf(
                    'Google regresó sin código de autorización. Parámetros recibidos: %s.',
                    $queryKeys === [] ? 'ninguno' : implode(', ', $queryKeys)
                ),
            ]);
        }

        try {
            $provider = Socialite::driver('google');

            if (method_exists($provider, 'stateless')) {
                $provider->stateless();
            }

            $googleUser = $provider->user();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('login')->withErrors([
                'google' => $this->oauthErrorMessage($exception),
            ]);
        }

        $email = Str::lower(trim((string) $googleUser->getEmail()));
        $googleId = trim((string) $googleUser->getId());
        $emailVerified = filter_var(
            data_get($googleUser->getRaw(), 'email_verified', false),
            FILTER_VALIDATE_BOOL
        );

        if ($email === '' || $googleId === '' || ! $emailVerified) {
            return redirect()->route('login')->withErrors([
                'google' => 'Google no proporcionó un correo verificado para esta cuenta.',
            ]);
        }

        $user = User::query()->where('google_id', $googleId)->first();
        $user ??= User::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if ($user?->google_id && ! hash_equals((string) $user->google_id, $googleId)) {
            return redirect()->route('login')->withErrors([
                'google' => 'Este correo ya está vinculado con otra cuenta de Google.',
            ]);
        }

        if (! $user) {
            $user = User::create([
                'name' => trim((string) $googleUser->getName()) ?: Str::before($email, '@'),
                'email' => $email,
                'google_id' => $googleId,
                'email_verified_at' => now(),
                'password' => Str::random(64),
            ]);
        } else {
            $user->forceFill([
                'google_id' => $googleId,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    private function isConfigured(): bool
    {
        return filled(config('services.google.client_id'))
            && filled(config('services.google.client_secret'))
            && filled(config('services.google.redirect'));
    }

    private function oauthErrorMessage(Throwable $exception): string
    {
        $detail = preg_replace('/\s+/', ' ', trim($exception->getMessage())) ?? '';
        $clientSecret = (string) config('services.google.client_secret');

        if ($clientSecret !== '') {
            $detail = str_replace($clientSecret, '[REDACTED]', $detail);
        }

        $detail = preg_replace(
            '/((?:client_secret|code|state|access_token|refresh_token|id_token)=)[^&\s]+/i',
            '$1[REDACTED]',
            $detail
        ) ?? $detail;
        $detail = preg_replace(
            '/(["\'](?:client_secret|code|state|access_token|refresh_token|id_token)["\']\s*:\s*["\'])[^"\']+/i',
            '$1[REDACTED]',
            $detail
        ) ?? $detail;
        $detail = preg_replace('/(Bearer\s+)[A-Za-z0-9._~-]+/i', '$1[REDACTED]', $detail) ?? $detail;

        return sprintf(
            'Google OAuth falló (%s): %s',
            class_basename($exception),
            Str::limit($detail !== '' ? $detail : 'La excepción no proporcionó detalles.', 600)
        );
    }

    private function configurationError(): RedirectResponse
    {
        return redirect()->route('login')->withErrors([
            'google' => 'El acceso con Google todavía no está configurado.',
        ]);
    }
}
