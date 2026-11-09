<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarengKirimController extends Controller
{
    public function index()
    {
        $user = Auth::user() ? Auth::user()->toArray() : session('user', [
            'name' => 'Grace Stanley',
            'location' => 'Beji, Depok',
        ]);

        $routes = [
            [
                'id' => 1,
                'origin' => 'Cluster Beji Indah',
                'destination' => 'Margonda & Kampus UI Depok',
                'vehicle' => 'Motor Gotong Royong (Box Belakang)',
                'departure_time' => '14:30 WIB Hari Ini',
                'status' => 'Berangkat 35 Menit Lagi',
                'carrier_name' => 'Rian Hidayat',
                'carrier_role' => 'Tetangga Cluster Beji Blok C',
                'carrier_avatar' => 'RH',
                'slots_total' => 4,
                'slots_filled' => 2,
                'cost_per_item' => 'Rp 6.000',
                'normal_cost' => 'Rp 18.000',
                'savings_percent' => '67%',
                'package_sizes' => 'Paket Kecil / Sedang (< 3 kg)',
                'drop_point' => 'Pos Satpam Blok B / Rumah No. 12',
                'image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'id' => 2,
                'origin' => 'Beji Indah & Kukusan',
                'destination' => 'Stasiun Depok Lama & ITC Depok',
                'vehicle' => 'Mobil MPV (Kapasitas Luas)',
                'departure_time' => '16:00 WIB Hari Ini',
                'status' => 'Tersedia 3 Slot Lagi',
                'carrier_name' => 'Bambang Soedirjo',
                'carrier_role' => 'Tetangga Cluster Beji Blok A',
                'carrier_avatar' => 'BS',
                'slots_total' => 6,
                'slots_filled' => 3,
                'cost_per_item' => 'Rp 8.000',
                'normal_cost' => 'Rp 24.000',
                'savings_percent' => '66%',
                'package_sizes' => 'Paket Sedang / Besar (< 10 kg)',
                'drop_point' => 'Lobby Cluster Utama',
                'image' => 'https://images.unsplash.com/photo-1616401784845-180882ba9ba8?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'id' => 3,
                'origin' => 'Cluster Beji Indah',
                'destination' => 'Cinere, Gandul & Sawangan',
                'vehicle' => 'Kurir Mitra BARENG Express',
                'departure_time' => '17:15 WIB Hari Ini',
                'status' => 'Sesi Terbuka',
                'carrier_name' => 'Fajar Santoso',
                'carrier_role' => 'Mitra Pooling BARENG',
                'carrier_avatar' => 'FS',
                'slots_total' => 5,
                'slots_filled' => 4,
                'cost_per_item' => 'Rp 7.500',
                'normal_cost' => 'Rp 22.000',
                'savings_percent' => '65%',
                'package_sizes' => 'Dokumen & Paket Reguler',
                'drop_point' => 'Pos Satpam Gerbang Depan',
                'image' => 'https://images.unsplash.com/photo-1566576912321-d58ddd7a6088?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'id' => 4,
                'origin' => 'Margonda & Beji',
                'destination' => 'Lenteng Agung & Cilandak (Jaksel)',
                'vehicle' => 'Motor Gotong Royong Searah Kerja',
                'departure_time' => 'Besok Pagi 07:30 WIB',
                'status' => 'Sesi Komuter Pagi',
                'carrier_name' => 'Doni Pradana',
                'carrier_role' => 'Komuter Tetangga',
                'carrier_avatar' => 'DP',
                'slots_total' => 3,
                'slots_filled' => 1,
                'cost_per_item' => 'Rp 10.000',
                'normal_cost' => 'Rp 28.000',
                'savings_percent' => '64%',
                'package_sizes' => 'Paket Ringan & Titipan Kantor',
                'drop_point' => 'Pos Satpam Blok B-12',
                'image' => 'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?auto=format&fit=crop&w=600&q=80',
            ],
        ];

        return view('customer.kirim.index', compact('user', 'routes'));
    }
}
