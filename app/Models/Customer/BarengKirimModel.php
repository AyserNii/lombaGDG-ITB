<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BarengKirimModel extends Model
{
    use HasFactory;
    public $timestamps = false;

    private static function get_data_source()
    {
        return [
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
                'normal_cost' => 'Rp 25.000',
                'savings_percent' => '68%',
                'package_sizes' => 'Bisa Paket Besar / Kardus',
                'drop_point' => 'Lobby Gerbang Timur',
                'image' => 'https://images.unsplash.com/photo-1519003722824-194d4455a60c?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'id' => 3,
                'origin' => 'Beji Margonda Raya',
                'destination' => 'Cilandak & Fatmawati Jaksel',
                'vehicle' => 'City Car (Rute Kerja Rutin)',
                'departure_time' => '07:30 WIB Besok Pagi',
                'status' => 'Sesi Berangkat Besok',
                'carrier_name' => 'Dini Anggraeni',
                'carrier_role' => 'Tetangga Blok D No. 04',
                'carrier_avatar' => 'DA',
                'slots_total' => 5,
                'slots_filled' => 4,
                'cost_per_item' => 'Rp 10.000',
                'normal_cost' => 'Rp 35.000',
                'savings_percent' => '71%',
                'package_sizes' => 'Dokumen / Box Sedang',
                'drop_point' => 'Titip Malam Ini di Rumah No. 04',
                'image' => 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?auto=format&fit=crop&w=600&q=80',
            ],
        ];
    }

    public static function total_sesikirim($limit = 100)
    {
        $data = self::get_data_source();
        return (object)['total' => count($data)];
    }

    public static function list_sesikirim($limit = 10, $offset = 0)
    {
        $data = self::get_data_source();
        return array_slice($data, $offset, $limit);
    }

    public static function total_sesikirimcari($cari, $limit = 100)
    {
        $data = self::get_data_source();
        $filtered = array_filter($data, function ($item) use ($cari) {
            return stripos($item['destination'], $cari) !== false || stripos($item['origin'], $cari) !== false;
        });
        return (object)['total' => count($filtered)];
    }

    public static function list_sesikirimcari($cari, $limit = 10, $offset = 0)
    {
        $data = self::get_data_source();
        $filtered = array_values(array_filter($data, function ($item) use ($cari) {
            return stripos($item['destination'], $cari) !== false || stripos($item['origin'], $cari) !== false;
        }));
        return array_slice($filtered, $offset, $limit);
    }

    public static function data_sesikirim($idsesi)
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

