<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarengBelanjaController extends Controller
{
    public function index()
    {
        $user = Auth::user() ? Auth::user()->toArray() : session('user', [
            'name' => 'Grace Stanley',
            'location' => 'Beji, Depok',
        ]);

        $activeSessions = [
            [
                'id' => 1,
                'store' => 'Segar Mart Margonda',
                'category' => 'Sayur & Buah Segar',
                'distance' => '350m dari clustermu',
                'host' => 'Budi Santoso',
                'host_role' => 'Host Cluster Beji',
                'host_avatar' => 'BS',
                'participants_count' => 4,
                'current_amount' => 165000,
                'target_amount' => 200000,
                'percent' => 82,
                'time_left' => 'Sisa 22 menit',
                'pickup_point' => 'Pos Satpam Blok B-12',
                'image' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=600&q=80',
                'popular_items' => ['Minyak Goreng 2L', 'Beras Ramos 5kg', 'Telur Ayam 1kg'],
                'saving_est' => 'Gratis Ongkir + Diskon 15%',
            ],
            [
                'id' => 2,
                'store' => 'Indomaret Fresh Cluster 4',
                'category' => 'Minimarket & Sembako',
                'distance' => '500m dari clustermu',
                'host' => 'Siti Rahma',
                'host_role' => 'Host RT 03',
                'host_avatar' => 'SR',
                'participants_count' => 3,
                'current_amount' => 85000,
                'target_amount' => 100000,
                'percent' => 85,
                'time_left' => 'Sisa 14 menit',
                'pickup_point' => 'Lobby Cluster Timur',
                'image' => 'https://images.unsplash.com/photo-1583258292688-d0213dc5a3a8?auto=format&fit=crop&w=600&q=80',
                'popular_items' => ['Gula Pasir 1kg', 'Susu UHT 1L', 'Kopi Bubuk'],
                'saving_est' => 'Ongkir dibagi 3 (Hemat Rp 9.000)',
            ],
            [
                'id' => 3,
                'store' => 'Grosir Beras & Sembako Bu Rini',
                'category' => 'Grosir Bulk',
                'distance' => '750m dari clustermu',
                'host' => 'Ahmad Fauzi',
                'host_role' => 'Host Komunitas Margonda',
                'host_avatar' => 'AF',
                'participants_count' => 6,
                'current_amount' => 420000,
                'target_amount' => 500000,
                'percent' => 84,
                'time_left' => 'Sisa 45 menit',
                'pickup_point' => 'Rumah Host No. 18',
                'image' => 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=600&q=80',
                'popular_items' => ['Beras Pandan Wangi 10kg', 'Minyak Goreng Dus', 'Tepung Terigu'],
                'saving_est' => 'Diskon Grosir 20% langsung!',
            ],
            [
                'id' => 4,
                'store' => 'Superindo Depok Town Square',
                'category' => 'Supermarket',
                'distance' => '1.1 km dari clustermu',
                'host' => 'Dewi Lestari',
                'host_role' => 'Host Beji Indah',
                'host_avatar' => 'DL',
                'participants_count' => 5,
                'current_amount' => 280000,
                'target_amount' => 300000,
                'percent' => 93,
                'time_left' => 'Sisa 9 menit (Hampir Penuh!)',
                'pickup_point' => 'Pos Satpam Gerbang Depan',
                'image' => 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=600&q=80',
                'popular_items' => ['Daging Ayam Segar', 'Deterjen Cair', 'Buah Apel Fuji'],
                'saving_est' => 'Free Delivery Superindo',
            ],
        ];

        $stores = [
            [
                'name' => 'Segar Mart Margonda',
                'category' => 'Sayur & Buah Organik',
                'rating' => '4.9',
                'promo' => 'Diskon Rp 25.000 jika belanja bareng min Rp 200rb',
                'image' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'name' => 'Grosir Beras Bu Rini',
                'category' => 'Pusat Sembako & Beras',
                'rating' => '4.8',
                'promo' => 'Beli 5 karung gratis ongkir & bonus minyak',
                'image' => 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'name' => 'Indomaret Fresh Beji',
                'category' => 'Minimarket & Kebutuhan Harian',
                'rating' => '4.7',
                'promo' => 'Tebus Murah Khusus Sesi BARENG Cluster',
                'image' => 'https://images.unsplash.com/photo-1583258292688-d0213dc5a3a8?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'name' => 'Pasar Tradisional Kemiri Muka',
                'category' => 'Bumbu, Ikan & Daging Segar',
                'rating' => '4.8',
                'promo' => 'Kurir Gotong Royong Langsung Pagi Hari',
                'image' => 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=400&q=80',
            ],
        ];

        return view('customer.belanja.index', compact('user', 'activeSessions', 'stores'));
    }
}
