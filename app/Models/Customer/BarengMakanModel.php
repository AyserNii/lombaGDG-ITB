<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BarengMakanModel extends Model
{
    use HasFactory;
    public $timestamps = false;

    private static function get_data_source()
    {
        return [
            [
                'id' => 1,
                'merchant' => 'Warung Nasi Goreng & Seafood Beji',
                'category' => 'Makanan Nusantara',
                'distance' => '250m dari lokasimu',
                'rating' => '4.9',
                'host' => 'Budi Santoso',
                'host_role' => 'Host Cluster Beji',
                'host_avatar' => 'BS',
                'slots_filled' => 3,
                'slots_max' => 5,
                'time_left' => 'Sisa 18 menit',
                'pickup_point' => 'Pos Satpam Blok B-12',
                'image' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=600&q=80',
                'ongkir_split' => 'Rp 3.500/orang',
                'normal_ongkir' => 'Rp 14.000',
                'popular_menus' => ['Nasgor Spesial', 'Kwetiau Goreng Sapi', 'Capcay Kuah'],
                'promo' => 'Diskon Ongkir 75% Dibagi Rata',
            ],
            [
                'id' => 2,
                'merchant' => 'Ayam Geprek & Sambal Bawang 88',
                'category' => 'Aneka Ayam & Pedas',
                'distance' => '450m dari lokasimu',
                'rating' => '4.8',
                'host' => 'Siti Rahma',
                'host_role' => 'Host RT 03',
                'host_avatar' => 'SR',
                'slots_filled' => 2,
                'slots_max' => 4,
                'time_left' => 'Sisa 12 menit',
                'pickup_point' => 'Lobby Cluster Timur',
                'image' => 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=600&q=80',
                'ongkir_split' => 'Rp 4.000/orang',
                'normal_ongkir' => 'Rp 16.000',
                'popular_menus' => ['Paket Geprek Keju', 'Geprek Sambal Matah', 'Tahu Tempe Goreng'],
                'promo' => 'Beli 3 Porsi Free Es Teh Manis',
            ],
            [
                'id' => 3,
                'merchant' => 'Mie Ayam & Bakso Urat Pak Min',
                'category' => 'Bakso & Mie',
                'distance' => '600m dari lokasimu',
                'rating' => '4.8',
                'host' => 'Ahmad Fauzi',
                'host_role' => 'Host Beji Indah',
                'host_avatar' => 'AF',
                'slots_filled' => 4,
                'slots_max' => 6,
                'time_left' => 'Sisa 25 menit',
                'pickup_point' => 'Rumah Host No. 18',
                'image' => 'https://images.unsplash.com/photo-1552611052-33e04de89d71?auto=format&fit=crop&w=600&q=80',
                'ongkir_split' => 'Rp 3.000/orang',
                'normal_ongkir' => 'Rp 15.000',
                'popular_menus' => ['Mie Ayam Bakso Jumbo', 'Pangsit Rebus', 'Bakso Urat Kuah'],
                'promo' => 'Promo Diskon Ongkir Komunitas',
            ],
            [
                'id' => 4,
                'merchant' => 'Sate Taichan & Kulit Senayan Margonda',
                'category' => 'Sate & Street Food',
                'distance' => '850m dari lokasimu',
                'rating' => '4.9',
                'host' => 'Dewi Lestari',
                'host_role' => 'Host Margonda',
                'host_avatar' => 'DL',
                'slots_filled' => 3,
                'slots_max' => 5,
                'time_left' => 'Sisa 30 menit',
                'pickup_point' => 'Pos Satpam Gerbang Depan',
                'image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80',
                'ongkir_split' => 'Rp 4.500/orang',
                'normal_ongkir' => 'Rp 18.000',
                'popular_menus' => ['Sate Daging 20 Tusuk', 'Sate Kulit Krispi', 'Lontong Sayur'],
                'promo' => 'Free Sambal Ekstra & Jeruk Nipis',
            ],
        ];
    }

    public static function total_sesimakan($limit = 100)
    {
        $data = self::get_data_source();
        return (object)['total' => count($data)];
    }

    public static function list_sesimakan($limit = 10, $offset = 0)
    {
        $data = self::get_data_source();
        return array_slice($data, $offset, $limit);
    }

    public static function total_sesimakancari($cari, $limit = 100)
    {
        $data = self::get_data_source();
        $filtered = array_filter($data, function ($item) use ($cari) {
            return stripos($item['merchant'], $cari) !== false || stripos($item['category'], $cari) !== false;
        });
        return (object)['total' => count($filtered)];
    }

    public static function list_sesimakancari($cari, $limit = 10, $offset = 0)
    {
        $data = self::get_data_source();
        $filtered = array_values(array_filter($data, function ($item) use ($cari) {
            return stripos($item['merchant'], $cari) !== false || stripos($item['category'], $cari) !== false;
        }));
        return array_slice($filtered, $offset, $limit);
    }

    public static function data_sesimakan($idsesi)
    {
        $data = self::get_data_source();
        foreach ($data as $item) {
            if ($item['id'] == $idsesi) {
                return (object)$item;
            }
        }
        return null;
    }
}
