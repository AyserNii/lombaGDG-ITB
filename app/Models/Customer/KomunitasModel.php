<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class KomunitasModel extends Model
{
    use HasFactory;
    public $timestamps = false;

    private static function get_community_list()
    {
        return [
            [
                'id' => 1,
                'name' => 'Cluster Makan Beji',
                'category' => 'Kuliner',
                'members' => '142 Warga',
                'icon' => 'food',
                'color' => 'bg-amber-500/10 text-amber-600 border-amber-200/50',
                'iconBg' => 'bg-amber-50 text-amber-600',
            ],
            [
                'id' => 2,
                'name' => 'Belanja Mingguan Margonda',
                'category' => 'Belanja',
                'members' => '89 Warga',
                'icon' => 'cart',
                'color' => 'bg-emerald-500/10 text-emerald-600 border-emerald-200/50',
                'iconBg' => 'bg-emerald-50 text-emerald-600',
            ],
            [
                'id' => 3,
                'name' => 'Kirim Bareng Depok',
                'category' => 'Logistik Rute',
                'members' => '64 Warga',
                'icon' => 'truck',
                'color' => 'bg-blue-500/10 text-blue-600 border-blue-200/50',
                'iconBg' => 'bg-blue-50 text-blue-600',
            ],
            [
                'id' => 4,
                'name' => 'Hemat & Hijau ITB',
                'category' => 'Eco-Community',
                'members' => '210 Anggota',
                'icon' => 'leaf',
                'color' => 'bg-teal-500/10 text-teal-600 border-teal-200/50',
                'iconBg' => 'bg-teal-50 text-teal-600',
            ],
        ];
    }

    public static function total_komunitas($limit = 100)
    {
        $data = self::get_community_list();
        return (object)['total' => count($data)];
    }

    public static function list_komunitas($limit = 10, $offset = 0)
    {
        $data = self::get_community_list();
        return array_slice($data, $offset, $limit);
    }

    public static function total_komunitascari($cari, $limit = 100)
    {
        $data = self::get_community_list();
        $filtered = array_filter($data, function ($item) use ($cari) {
            return stripos($item['name'], $cari) !== false || stripos($item['category'], $cari) !== false;
        });
        return (object)['total' => count($filtered)];
    }

    public static function list_komunitascari($cari, $limit = 10, $offset = 0)
    {
        $data = self::get_community_list();
        $filtered = array_values(array_filter($data, function ($item) use ($cari) {
            return stripos($item['name'], $cari) !== false || stripos($item['category'], $cari) !== false;
        }));
        return array_slice($filtered, $offset, $limit);
    }

    public static function data_komunitas($id)
    {
        $data = self::get_community_list();
        foreach ($data as $item) {
            if ($item['id'] == $id) {
                return (object)$item;
            }
        }
        return null;
    }
}

