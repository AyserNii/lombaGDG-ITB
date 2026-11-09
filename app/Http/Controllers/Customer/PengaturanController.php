<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PengaturanController extends Controller
{
    public function index()
    {
        $user = Auth::user() ? Auth::user()->toArray() : session('user');

        if (!$user) {
            $dbUser = User::where('email', 'grace@bareng.id')->first();
            if ($dbUser) {
                $user = $dbUser->toArray();
            } else {
                $user = [
                    'name' => 'Grace Stanley',
                    'email' => 'grace@bareng.id',
                    'phone' => '081234567890',
                    'role' => 'Customer · Cluster Beji',
                    'role_type' => 'customer',
                    'location' => 'Beji, Depok',
                    'pickup_point' => 'Pos Satpam Cluster Beji Blok B-12',
                    'matching_radius' => 800,
                    'avatar' => null,
                    'savings' => 'Rp 95.4k',
                    'sessions_count' => 12,
                    'co2_saved' => '28.4 kg',
                    'badge' => 'Top Saver',
                ];
            }
            session(['user' => $user]);
        }

        // Additional default settings if not yet in array
        $user['phone'] = $user['phone'] ?? '081234567890';
        $user['pickup_point'] = $user['pickup_point'] ?? 'Pos Satpam Cluster Beji Blok B-12';
        $user['matching_radius'] = $user['matching_radius'] ?? 800;
        $user['notes'] = $user['notes'] ?? 'Titip di loker paket samping pos satpam jika tidak ada di rumah.';
        $user['notif_whatsapp'] = $user['notif_whatsapp'] ?? true;
        $user['notif_email'] = $user['notif_email'] ?? true;
        $user['eco_mode'] = $user['eco_mode'] ?? true;

        return view('customer.pengaturan.index', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'location' => 'required|string|max:120',
            'pickup_point' => 'nullable|string|max:200',
            'matching_radius' => 'nullable|integer',
            'notes' => 'nullable|string|max:300',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:6|confirmed',
        ]);

        $currentUser = Auth::user() ? Auth::user() : User::where('email', session('user.email', 'grace@bareng.id'))->first();

        if ($currentUser) {
            $currentUser->name = $request->name;
            if ($request->filled('phone')) {
                $currentUser->phone = $request->phone;
            }
            $currentUser->location = $request->location;

            if ($request->filled('new_password')) {
                if ($request->filled('current_password') && !Hash::check($request->current_password, $currentUser->password)) {
                    return back()->withErrors(['current_password' => 'Password lama yang dimasukkan tidak sesuai.']);
                }
                $currentUser->password = Hash::make($request->new_password);
            }

            $currentUser->save();
            $userData = $currentUser->toArray();
        } else {
            $userData = session('user', []);
            $userData['name'] = $request->name;
            $userData['phone'] = $request->phone;
            $userData['location'] = $request->location;
        }

        $userData['pickup_point'] = $request->pickup_point ?? 'Pos Satpam Cluster Beji Blok B-12';
        $userData['matching_radius'] = (int) ($request->matching_radius ?? 800);
        $userData['notes'] = $request->notes ?? '';
        $userData['notif_whatsapp'] = $request->boolean('notif_whatsapp', true);
        $userData['notif_email'] = $request->boolean('notif_email', true);
        $userData['eco_mode'] = $request->boolean('eco_mode', true);

        session(['user' => $userData]);

        return back()->with('success', 'Pengaturan profil dan preferensi gotong royong berhasil diperbarui!');
    }
}
