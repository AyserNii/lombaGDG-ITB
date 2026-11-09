<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        if (Auth::check() || session()->has('user')) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $location = $validated['location'] ?? 'Beji, Depok';

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'role' => 'Customer · ' . $location,
            'role_type' => 'customer',
            'location' => $location,
            'avatar' => null,
            'phone' => $validated['phone'] ?? null,
            'savings' => 'Rp 0',
            'sessions_count' => 0,
            'co2_saved' => '0 kg',
            'badge' => 'New Explorer',
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        session(['user' => $user->toArray()]);

        return redirect()->route('dashboard')->with('success', 'Akun berhasil dibuat! Selamat bergabung di BARENG.');
    }
}
