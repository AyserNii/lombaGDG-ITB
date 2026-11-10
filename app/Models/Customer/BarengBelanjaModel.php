<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BarengBelanjaModel extends Model
{
    use HasFactory;
    public $timestamps = false;

    private static function get_data_source()
    {
        return [
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
                'image' => 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=600&q=80',
                'popular_items' => ['Susu UHT 1L', 'Roti Tawar Gandum', 'Gula Pasir 1kg'],
                'saving_est' => 'Hemat Rp 12.000 Ongkir',
            ],
            [
                'id' => 3,
                'store' => 'Pasar Kemiri Muka Beji',
                'category' => 'Bumbu Dapur & Lauk Pauk',
                'distance' => '750m dari clustermu',
                'host' => 'Dewi Lestari',
                'host_role' => 'Host Margonda',
                'host_avatar' => 'DL',
                'participants_count' => 5,
                'current_amount' => 210000,
                'target_amount' => 250000,
                'percent' => 84,
                'time_left' => 'Sisa 40 menit',
                'pickup_point' => 'Taman Blok C',
                'image' => 'https://images.unsplash.com/photo-1506484381205-f7945653044d?auto=format&fit=crop&w=600&q=80',
                'popular_items' => ['Ayam Fillet 1kg', 'Bawang Merah & Putih', 'Cabai Rawit Merah'],
                'saving_est' => 'Bagi Ongkir Rp 2.500/orang',
            ],
            [
                'id' => 4,
                'store' => 'Toko Daging Nusantara Beji',
                'category' => 'Daging Sapi & Unggas',
                'distance' => '1.2km dari clustermu',
                'host' => 'Rahmat Hidayat',
                'host_role' => 'Host Beji Timur',
                'host_avatar' => 'RH',
                'participants_count' => 3,
                'current_amount' => 280000,
                'target_amount' => 350000,
                'percent' => 80,
                'time_left' => 'Sisa 55 menit',
                'pickup_point' => 'Rumah Host No. 07',
                'image' => 'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?auto=format&fit=crop&w=600&q=80',
                'popular_items' => ['Daging Rendang 1kg', 'Iga Sapi Potong', 'Daging Giling'],
                'saving_est' => 'Diskon Kolektif 10%',
            ],
        ];
    }

    public static function total_sesibelanja($limit = 100)
    {
        $data = self::get_data_source();
        return (object)['total' => count($data)];
    }

    public static function list_sesibelanja($limit = 10, $offset = 0)
    {
        $data = self::get_data_source();
        return array_slice($data, $offset, $limit);
    }

    public static function total_sesibelanjacari($cari, $limit = 100)
    {
        $data = self::get_data_source();
        $filtered = array_filter($data, function ($item) use ($cari) {
            return stripos($item['store'], $cari) !== false || stripos($item['category'], $cari) !== false;
        });
        return (object)['total' => count($filtered)];
    }

    public static function list_sesibelanjacari($cari, $limit = 10, $offset = 0)
    {
        $data = self::get_data_source();
        $filtered = array_values(array_filter($data, function ($item) use ($cari) {
            return stripos($item['store'], $cari) !== false || stripos($item['category'], $cari) !== false;
        }));
        return array_slice($filtered, $offset, $limit);
    }

    public static function data_sesibelanja($idsesi)
    {
        $data = self::get_data_source();
        foreach ($data as $item) {
            if ($item['id'] == $idsesi) {
                return (object)$item;
            }
        }
        return null;
    }

    public static function list_mitratoko()
    {
        return [
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
    }
}

