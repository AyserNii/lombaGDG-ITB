<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SesiAktifController extends Controller
{
    public function index()
    {
        $user = Auth::user() ? Auth::user()->toArray() : session('user', [
            'name' => 'Grace Stanley',
            'location' => 'Beji, Depok',
        ]);

        // Sesi yang sedang diikuti pengguna
        $mySessions = [
            [
                'id' => 101,
                'type' => 'makan',
                'title' => 'Makan Siang · Warung Nasi Goreng Beji',
                'merchant' => 'Warung Nasi Goreng Beji',
                'status_step' => 3, // 1: Kumpul, 2: Dipesan, 3: Sedang Diantar, 4: Tiba di Titik Temu
                'status_label' => 'Sedang Diantar Kurir Gotong Royong',
                'eta' => 'Tiba dalam 12 Menit',
                'host' => 'Budi Santoso (Host)',
                'pickup_point' => 'Pos Satpam Cluster Beji Blok B-12',
                'my_items' => '1x Nasi Goreng Spesial + 1x Es Teh Manis',
                'my_total' => 'Rp 28.000',
                'saved_ongkir' => 'Rp 14.000 (Ongkir dibagi 4 orang)',
                'participants_count' => 4,
                'image' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'id' => 102,
                'type' => 'belanja',
                'title' => 'Belanja Sembako · Segar Mart Margonda',
                'merchant' => 'Segar Mart Margonda',
                'status_step' => 1,
                'status_label' => 'Mengumpulkan Kuota Diskon Grosir',
                'eta' => 'Closing dalam 22 Menit',
                'host' => 'Grace Stanley (Kamu Host)',
                'pickup_point' => 'Rumah Host Blok B-12',
                'my_items' => '1x Minyak Goreng 2L + 1x Beras 5kg',
                'my_total' => 'Rp 92.000',
                'saved_ongkir' => 'Free Delivery Target Rp 200rb',
                'participants_count' => 4,
                'image' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'id' => 103,
                'type' => 'kirim',
                'title' => 'Kirim Paket · Rute Margonda & Kampus UI',
                'merchant' => 'Kurir Gotong Royong Tetangga',
                'status_step' => 2,
                'status_label' => 'Menunggu Jadwal Pickup Jam 14:30',
                'eta' => 'Berangkat 35 Menit Lagi',
                'host' => 'Rian Hidayat (Driver)',
                'pickup_point' => 'Pos Satpam Gerbang Depan',
                'my_items' => '1x Dokumen Kampus (0.5 kg)',
                'my_total' => 'Rp 6.000',
                'saved_ongkir' => 'Hemat Rp 12.000 vs Kurir Reguler',
                'participants_count' => 3,
                'image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=600&q=80',
            ],
        ];

        // Sesi publik lain di sekitar yang bisa diikuti
        $exploreSessions = [
            [
                'id' => 201,
                'type' => 'makan',
                'type_label' => 'Bareng Makan',
                'type_color' => 'bg-amber-50 text-amber-700 border-amber-200',
                'title' => 'Ayam Geprek 88 Beji',
                'distance' => '420m dari lokasimu',
                'closing_in' => '18 menit lagi',
                'slots' => '3 dari 5 slot terisi',
                'ongkir_split' => 'Rp 3.500/orang',
                'host' => 'Siti Rahma',
                'pickup' => 'Lobby Cluster Timur',
                'image' => 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'id' => 202,
                'type' => 'belanja',
                'type_label' => 'Bareng Belanja',
                'type_color' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'title' => 'Grosir Beras Bu Rini',
                'distance' => '750m dari lokasimu',
                'closing_in' => '45 menit lagi',
                'slots' => '6 orang join (Diskon 20%)',
                'ongkir_split' => 'Gratis Ongkir',
                'host' => 'Ahmad Fauzi',
                'pickup' => 'Rumah Host No. 18',
                'image' => 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'id' => 203,
                'type' => 'kirim',
                'type_label' => 'Bareng Kirim',
                'type_color' => 'bg-blue-50 text-blue-700 border-blue-200',
                'title' => 'Rute Stasiun Depok Lama & ITC',
                'distance' => '600m dari lokasimu',
                'closing_in' => 'Jam 16.00 WIB',
                'slots' => '3 dari 6 slot terisi',
                'ongkir_split' => 'Rp 8.000/paket',
                'host' => 'Bambang Soedirjo',
                'pickup' => 'Pos Satpam Blok B',
                'image' => 'https://images.unsplash.com/photo-1616401784845-180882ba9ba8?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'id' => 204,
                'type' => 'makan',
                'type_label' => 'Bareng Makan',
                'type_color' => 'bg-amber-50 text-amber-700 border-amber-200',
                'title' => 'Mie Ayam & Bakso Pak Min',
                'distance' => '500m dari lokasimu',
                'closing_in' => '25 menit lagi',
                'slots' => '4 orang join',
                'ongkir_split' => 'Rp 4.000/orang',
                'host' => 'Dewi Lestari',
                'pickup' => 'Pos Satpam Gerbang Depan',
                'image' => 'https://images.unsplash.com/photo-1552611052-33e04de89d71?auto=format&fit=crop&w=400&q=80',
            ],
        ];

        return view('customer.sesi.index', compact('user', 'mySessions', 'exploreSessions'));
    }
}
