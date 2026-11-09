<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class KomunitasController extends Controller
{
    public function index()
    {
        $user = Auth::user() ? Auth::user()->toArray() : session('user');

        if (!$user) {
            $dbUser = User::where('email', 'grace@bareng.id')->first();
            $user = $dbUser ? $dbUser->toArray() : [
                'name' => 'Grace Stanley',
                'email' => 'grace@bareng.id',
                'role' => 'Customer · Cluster Beji',
                'location' => 'Beji, Depok',
                'avatar' => null,
            ];
            session(['user' => $user]);
        }

        $myCommunities = [
            [
                'name' => 'Cluster Makan Beji',
                'category' => 'Kuliner',
                'members' => '142 Warga',
                'icon' => 'food',
                'color' => 'bg-amber-500/10 text-amber-600 border-amber-200/50',
                'iconBg' => 'bg-amber-50 text-amber-600',
            ],
            [
                'name' => 'Belanja Mingguan Margonda',
                'category' => 'Belanja',
                'members' => '89 Warga',
                'icon' => 'cart',
                'color' => 'bg-emerald-500/10 text-emerald-600 border-emerald-200/50',
                'iconBg' => 'bg-emerald-50 text-emerald-600',
            ],
            [
                'name' => 'Kirim Bareng Depok',
                'category' => 'Logistik Rute',
                'members' => '64 Warga',
                'icon' => 'truck',
                'color' => 'bg-blue-500/10 text-blue-600 border-blue-200/50',
                'iconBg' => 'bg-blue-50 text-blue-600',
            ],
            [
                'name' => 'Hemat & Hijau ITB',
                'category' => 'Eco-Community',
                'members' => '210 Anggota',
                'icon' => 'leaf',
                'color' => 'bg-teal-500/10 text-teal-600 border-teal-200/50',
                'iconBg' => 'bg-teal-50 text-teal-600',
            ],
        ];

        $stories = [
            ['name' => 'Ceritamu', 'initials' => 'GS', 'bg' => 'bg-blue-600', 'add' => true],
            ['name' => 'Mary', 'initials' => 'MJ', 'bg' => 'bg-indigo-600'],
            ['name' => 'James', 'initials' => 'JB', 'bg' => 'bg-emerald-600'],
            ['name' => 'Andi', 'initials' => 'AP', 'bg' => 'bg-amber-600'],
            ['name' => 'Budi', 'initials' => 'BS', 'bg' => 'bg-rose-600'],
            ['name' => 'Sari', 'initials' => 'SW', 'bg' => 'bg-teal-600'],
            ['name' => 'Dimas', 'initials' => 'DP', 'bg' => 'bg-violet-600'],
        ];

        $posts = [
            [
                'user' => 'Mary Johnson',
                'initials' => 'MJ',
                'avatar_bg' => 'bg-indigo-600',
                'role' => 'Session Creator · Beji',
                'time' => '2 jam lalu',
                'photo' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1200&q=80',
                'text' => 'Sesi BARENG MAKAN masih muat 2 orang! Nasi goreng + ayam geprek, ongkir dibagi rata. Estimasi sampai jam 12:30 WIB.',
                'likes' => '48',
                'comments' => '12',
                'type' => 'BARENG MAKAN',
                'type_icon' => 'food',
                'badge_color' => 'bg-orange-50 text-orange-600 border-orange-200',
            ],
            [
                'user' => 'James Brown',
                'initials' => 'JB',
                'avatar_bg' => 'bg-emerald-600',
                'role' => 'Neighbor · Margonda',
                'time' => '5 jam lalu',
                'photo' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1200&q=80',
                'text' => 'Belanja mingguan bareng cluster 4. Minyak, beras, telur sudah hampir capai minimum diskon toko. Siapa mau titip lagi?',
                'likes' => '76',
                'comments' => '21',
                'type' => 'BARENG BELANJA',
                'type_icon' => 'cart',
                'badge_color' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
            ],
            [
                'user' => 'Komunitas Hemat Depok',
                'initials' => 'KH',
                'avatar_bg' => 'bg-teal-600',
                'role' => 'Official Community · 1.2k members',
                'time' => 'Kemarin',
                'photo' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=1200&q=80',
                'text' => 'Update dampak minggu ini: 128 perjalanan kendaraan berhasil digabung dan konsolidasi sampah plastik kemasan turun 34%. Terima kasih semua partisipan!',
                'likes' => '210',
                'comments' => '34',
                'type' => 'DAMPAK HIJAU',
                'type_icon' => 'leaf',
                'badge_color' => 'bg-teal-50 text-teal-600 border-teal-200',
            ],
        ];

        return view('community', compact('user', 'myCommunities', 'stories', 'posts'));
    }
}
