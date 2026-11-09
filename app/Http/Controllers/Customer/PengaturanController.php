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
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        return view('customer.pengaturan.index', [
            'user' => $user->toArray(),
            'userModel' => $user,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'nullable|string|max:50|unique:users,username,' . $user->id,
            'website' => 'nullable|string|max:200',
            'bio' => 'nullable|string|max:300',
            'gender' => 'required|in:male,female,custom,prefer_not_to_say',
            'phone' => 'nullable|string|max:20',
            'location' => 'required|string|max:120',
            'avatar_cropped' => 'nullable|string',
            'avatar_url' => 'nullable|string|max:500',
            'avatar_file' => 'nullable|image|max:5120',
            'pickup_point' => 'nullable|string|max:200',
            'matching_radius' => 'nullable|integer',
            'notes' => 'nullable|string|max:300',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->name = $request->name;
        if ($request->filled('username')) {
            $user->username = strtolower(trim($request->username));
        }
        $user->website = $request->website;
        $user->bio = $request->bio;
        $user->gender = $request->gender;
        $user->phone = $request->phone;
        $user->location = $request->location;

        // Handle Avatar (Cropped Base64, Direct File Upload to public/uploads/avatars, or URL)
        if ($request->filled('avatar_cropped')) {
            $b64 = $request->avatar_cropped;
            if (str_starts_with($b64, 'data:image')) {
                $parts = explode(',', $b64);
                if (isset($parts[1])) {
                    $imgData = base64_decode($parts[1]);
                    $filename = 'avatar_' . $user->id . '_' . time() . '.png';
                    file_put_contents(public_path('uploads/avatars/' . $filename), $imgData);
                    $user->avatar = '/uploads/avatars/' . $filename;
                }
            }
        } elseif ($request->hasFile('avatar_file')) {
            $file = $request->file('avatar_file');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/avatars'), $filename);
            $user->avatar = '/uploads/avatars/' . $filename;
        } elseif ($request->filled('avatar_url')) {
            $user->avatar = $request->avatar_url;
        }

        // Password change
        if ($request->filled('new_password')) {
            if ($request->filled('current_password') && !Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password lama yang dimasukkan tidak sesuai.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        // Update session
        $userData = $user->toArray();
        $userData['pickup_point'] = $request->pickup_point ?? 'Pos Satpam Cluster Beji Blok B-12';
        $userData['matching_radius'] = (int) ($request->matching_radius ?? 800);
        $userData['notes'] = $request->notes ?? '';
        $userData['notif_whatsapp'] = $request->boolean('notif_whatsapp', true);
        $userData['notif_email'] = $request->boolean('notif_email', true);
        $userData['eco_mode'] = $request->boolean('eco_mode', true);

        session(['user' => $userData]);

        return back()->with('success', 'Profil dan preferensi akun berhasil diperbarui!');
    }
}
