<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\PengaturanModel as MainModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

header('Cache-Control: no-store, private, no-cache, must-revalidate');
header('Cache-Control: pre-check=0, post-check=0, max-age=0, max-stale = 0', false);
header('Pragma: public');
header('Pragma: no-cache');

class PengaturanController extends Controller
{
    private $role = 'customer';
    private $module = 'akun';
    private $page = 'pengaturan';
    private $title = 'Pengaturan Akun';

    private $sessionNamespace;
    private $basePath;
    private $routePrefix;
    private $routeName;
    private $lastPageKey;
    private $alertKey;

    public function __construct()
    {
        $this->routeName = '/' . $this->role . '/' . $this->page;
        $this->basePath = $this->role . '.' . $this->module . '.';
        $this->routePrefix = $this->role . '.' . $this->page . '.';
        $this->alertKey = 'notifikasi' . $this->role . $this->module;
        $this->lastPageKey = 'url' . $this->role . $this->module . $this->page;
        $this->sessionNamespace = 'sesi' . $this->role . $this->module;
    }

    public function list(Request $request, $halaman = '')
    {
        return $this->index();
    }

    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Hitung persentase kelengkapan profil
        $completion = 0;
        if (!empty($user->name)) $completion += 20;
        if (!empty($user->username)) $completion += 15;
        if (!empty($user->headline)) $completion += 15;
        if (!empty($user->avatar)) $completion += 20;
        if (!empty($user->bio)) $completion += 15;
        if (!empty($user->location) || !empty($user->phone)) $completion += 15;

        $datakonten['judulcore'] = $this->title;
        $datakonten['user'] = $user->toArray();
        $datakonten['userModel'] = $user;
        $datakonten['completion'] = $completion;
        $datakonten['basePath'] = $this->basePath;
        $datakonten['routePrefix'] = $this->routePrefix;
        $datakonten['sessionNamespace'] = $this->sessionNamespace;

        return view('customer.pengaturan.index', $datakonten);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'nullable|string|max:50|unique:users,username,' . $user->id,
            'headline' => 'nullable|string|max:150',
            'website' => 'nullable|string|max:200',
            'bio' => 'nullable|string|max:500',
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

        $updateData = [
            'name' => $request->name,
            'headline' => $request->headline,
            'website' => $request->website,
            'bio' => $request->bio,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'location' => $request->location,
        ];

        if ($request->filled('username')) {
            $updateData['username'] = strtolower(trim($request->username));
        }

        // Handle Avatar (Cropped Base64, Direct File Upload to public/uploads/avatars, or URL)
        if ($request->filled('avatar_cropped')) {
            $b64 = $request->avatar_cropped;
            if (str_starts_with($b64, 'data:image')) {
                $parts = explode(',', $b64);
                if (isset($parts[1])) {
                    $imgData = base64_decode($parts[1]);
                    $filename = 'avatar_' . $user->id . '_' . time() . '.png';
                    file_put_contents(public_path('uploads/avatars/' . $filename), $imgData);
                    $updateData['avatar'] = '/uploads/avatars/' . $filename;
                }
            }
        } elseif ($request->hasFile('avatar_file')) {
            $file = $request->file('avatar_file');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/avatars'), $filename);
            $updateData['avatar'] = '/uploads/avatars/' . $filename;
        } elseif ($request->filled('avatar_url')) {
            $updateData['avatar'] = $request->avatar_url;
        }

        // Password change
        if ($request->filled('new_password')) {
            if ($request->filled('current_password') && !Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password lama yang dimasukkan tidak sesuai.']);
            }
            $updateData['password'] = Hash::make($request->new_password);
        }

        MainModel::perbarui_profil($user->id, $updateData);

        // Update session
        $freshUser = User::find($user->id);
        $userData = $freshUser->toArray();
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
