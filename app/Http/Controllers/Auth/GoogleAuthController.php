<?php

namespace App\Http\Controllers\Auth;

header('Cache-Control: no-store, private, no-cache, must-revalidate');
header('Cache-Control: pre-check=0, post-check=0, max-age=0, max-stale = 0', false);
header('Pragma: public');
header('Pragma: no-cache');

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

            if (!$googleUser || empty($googleUser->getEmail())) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Tidak dapat mengambil informasi akun dari Google. Silakan coba lagi.',
                ]);
            }

            $email = strtolower(trim($googleUser->getEmail()));
            $googleId = (string) $googleUser->getId();

            // 1. Cari user berdasarkan google_id
            $user = User::where('google_id', $googleId)->first();

            // 2. Jika belum ditemukan dengan google_id, cari berdasarkan email
            if (!$user) {
                $user = User::where('email', $email)->first();

                if ($user) {
                    // Tautkan google_id ke akun yang sudah ada
                    $user->google_id = $googleId;
                    if (empty($user->avatar) && !empty($googleUser->getAvatar())) {
                        $user->avatar = $googleUser->getAvatar();
                    }
                    $user->save();
                }
            }

            // 3. Jika user belum ada sama sekali, buat user baru (Registrasi via Google)
            if (!$user) {
                $name = $googleUser->getName() ?: 'User Google';
                
                // Buat username unik dari prefix email
                $rawPrefix = explode('@', $email)[0];
                $baseUsername = Str::slug($rawPrefix, '') ?: 'user';
                $username = $baseUsername;
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . $counter;
                    $counter++;
                }

                $user = User::create([
                    'name'           => $name,
                    'username'       => $username,
                    'email'          => $email,
                    'google_id'      => $googleId,
                    'password'       => Hash::make(Str::random(32)),
                    'avatar'         => $googleUser->getAvatar(),
                    'role'           => 'Customer · Beji, Depok',
                    'role_type'      => 'customer',
                    'location'       => 'Beji, Depok',
                    'savings'        => 'Rp 0',
                    'sessions_count' => 0,
                    'co2_saved'      => '0 kg',
                    'badge'          => 'New Explorer',
                ]);
            }

            // 4. Login-kan user dan perbarui session
            Auth::login($user, true);
            $request->session()->regenerate();
            session(['user' => $user->toArray()]);

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Berhasil masuk dengan Google! Selamat datang, ' . $user->name . '.');

        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal masuk melalui Google. Pastikan izin telah diberikan atau coba lagi.',
            ]);
        }
    }
}

