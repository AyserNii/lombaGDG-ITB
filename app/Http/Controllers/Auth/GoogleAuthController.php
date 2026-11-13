<?php

namespace App\Http\Controllers\Auth;

header('Cache-Control: no-store, private, no-cache, must-revalidate');
header('Cache-Control: pre-check=0, post-check=0, max-age=0, max-stale = 0', false);
header('Pragma: public');
header('Pragma: no-cache');

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect pengguna ke halaman otentikasi Google
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Tangani callback dari Google OAuth
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            // Gunakan stateless() untuk keandalan maksimal dan menghindari InvalidStateException
            $googleUser = Socialite::driver('google')->stateless()->user();

            if (! $googleUser || empty($googleUser->getEmail())) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Tidak dapat mengambil informasi akun dari Google. Silakan coba lagi.',
                ]);
            }

            $email = strtolower(trim($googleUser->getEmail()));
            $googleId = (string) $googleUser->getId();
            $googleAvatarUrl = $googleUser->getAvatar();
            $isNewUser = false;

            // 1. Cari user berdasarkan google_id
            $user = User::findByGoogleId($googleId);

            // 2. Jika belum ditemukan dengan google_id, cari berdasarkan email
            if (! $user) {
                $user = User::findByEmail($email);

                if ($user) {
                    // Tautkan google_id ke akun yang sudah ada
                    $user->google_id = $googleId;
                }
            }

            if ($user) {
                if (empty($user->avatar) || str_starts_with($user->avatar, 'https://lh3.googleusercontent.com/')) {
                    $user->avatar = $this->cacheGoogleAvatar($googleAvatarUrl, $googleId);
                }
                $user->save();
            }

            // 3. Jika user belum ada sama sekali, buat user baru (Registrasi via Google)
            if (! $user) {
                $isNewUser = true;
                $name = $googleUser->getName() ?: 'User Google';
                $googleAvatar = $this->cacheGoogleAvatar($googleAvatarUrl, $googleId);

                // Buat username unik dari prefix email
                $rawPrefix = explode('@', $email)[0];
                $baseUsername = Str::slug($rawPrefix, '') ?: 'user';
                $username = $baseUsername;
                $counter = 1;
                while (User::usernameExists($username)) {
                    $username = $baseUsername.$counter;
                    $counter++;
                }

                $user = User::create([
                    'name' => $name,
                    'username' => $username,
                    'email' => $email,
                    'google_id' => $googleId,
                    'password' => Hash::make(Str::random(32)),
                    'avatar' => $googleAvatar,
                    'role' => 'Customer',
                    'role_type' => 'customer',
                    'location' => 'Belum diatur',
                    'savings' => 'Rp 0',
                    'sessions_count' => 0,
                    'co2_saved' => '0 kg',
                    'badge' => 'New Explorer',
                ]);
            }

            // 4. Login-kan user dan perbarui session
            Auth::login($user, true);
            $request->session()->regenerate();
            session(['user' => $user->toArray()]);

            $redirect = $isNewUser
                ? redirect()->route('settings')
                : redirect()->intended(route('dashboard'));

            $message = 'Berhasil masuk dengan Google! Selamat datang, '.$user->name.'.';
            if ($isNewUser) {
                $message .= ' Lengkapi lokasi domisili di pengaturan profil.';
            }

            return $redirect->with('success', $message);

        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('login')->withErrors([
                'email' => 'Gagal masuk melalui Google. Pastikan izin telah diberikan atau coba lagi.',
            ]);
        }
    }

    private function cacheGoogleAvatar(?string $avatarUrl, string $googleId): ?string
    {
        if (! $avatarUrl) {
            return null;
        }

        $parts = parse_url($avatarUrl);
        $host = strtolower($parts['host'] ?? '');
        if (($parts['scheme'] ?? '') !== 'https'
            || ($host !== 'googleusercontent.com' && ! str_ends_with($host, '.googleusercontent.com'))) {
            Log::warning('Google returned an avatar URL outside the allowed host.', ['google_id' => $googleId]);

            return null;
        }

        try {
            $response = Http::timeout(5)
                ->withOptions(['allow_redirects' => false])
                ->get($avatarUrl);
        } catch (ConnectionException $exception) {
            Log::warning('Could not download the Google profile photo.', [
                'google_id' => $googleId,
                'error' => $exception->getMessage(),
            ]);

            return $avatarUrl;
        }

        $contentType = strtolower(trim(explode(';', $response->header('Content-Type', ''))[0]));
        $extension = match ($contentType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => null,
        };
        $contents = $response->body();

        if (! $response->successful() || ! $extension || strlen($contents) > 5 * 1024 * 1024) {
            Log::warning('Google profile photo response was not a supported image.', [
                'google_id' => $googleId,
                'status' => $response->status(),
                'content_type' => $contentType,
            ]);

            return $avatarUrl;
        }

        $directory = public_path('uploads/avatars');
        File::ensureDirectoryExists($directory);
        $filename = 'google_'.hash('sha256', $googleId).'.'.$extension;
        if (File::put($directory.DIRECTORY_SEPARATOR.$filename, $contents) === false) {
            Log::warning('Could not save the Google profile photo locally.', ['google_id' => $googleId]);

            return $avatarUrl;
        }

        return '/uploads/avatars/'.$filename;
    }
}
