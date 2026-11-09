<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SocialSeeder extends Seeder
{
    public function run(): void
    {
        $resya = User::where('email', 'resya1@gmail.com')->first();
        $dimas = User::where('email', 'dimas1@gmail.com')->first();
        $demoEmails = [
            'jall@bareng.id',
            'rfiannsyh@bareng.id',
            'ndis@bareng.id',
            'fitriani@bareng.id',
            'ibrar@bareng.id',
            'meza@bareng.id',
            'hendra@bareng.id',
            'qaliee@bareng.id',
        ];
        $demoUserIds = User::whereIn('email', $demoEmails)->pluck('id')->all();

        DB::transaction(function () use ($demoUserIds, $demoEmails, $resya, $dimas) {
            Post::whereIn('user_id', $demoUserIds)->delete();
            if ($resya) {
                Post::where('user_id', $resya->id)->delete();
            }

            if ($demoUserIds) {
                DB::table('notifications')->whereIn('actor_id', $demoUserIds)->delete();
                User::whereIn('id', $demoUserIds)->delete();
            }

            if ($resya) {
                $resya->update([
                    'username' => 'aysernii',
                    'bio' => 'Gotong royong & hemat ongkir bareng cluster Beji',
                    'website' => null,
                    'gender' => 'prefer_not_to_say',
                    'note' => null,
                ]);
            }

            if ($dimas) {
                $dimas->update([
                    'username' => 'dimas_p',
                    'bio' => 'Session Creator · Margonda',
                    'website' => null,
                    'gender' => 'male',
                    'note' => null,
                ]);
            }

            DB::table('users')->whereIn('email', $demoEmails)->delete();
        });
    }
}
