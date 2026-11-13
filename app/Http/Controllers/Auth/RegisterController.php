<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\CountryList;
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

        return view('auth.register', ['countries' => CountryList::options()]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'location' => 'required|string|max:255',
            'location_country' => 'required|string|max:100',
            'location_latitude' => 'nullable|numeric|between:-90,90|required_with:location_longitude',
            'location_longitude' => 'nullable|numeric|between:-180,180|required_with:location_latitude',
            'phone' => 'nullable|string|max:20',
        ]);

        $location = $validated['location'];

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'role' => 'Customer · '.$location,
            'role_type' => 'customer',
            'location' => $location,
            'location_country' => $validated['location_country'],
            'location_latitude' => $validated['location_latitude'] ?? null,
            'location_longitude' => $validated['location_longitude'] ?? null,
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
