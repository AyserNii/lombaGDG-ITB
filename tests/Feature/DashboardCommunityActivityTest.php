<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Customer\BarengMakanModel as FoodSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardCommunityActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_greeting_and_profile_summary_use_the_authenticated_users_data(): void
    {
        $user = User::factory()->create([
            'name' => 'Ayu Sari',
            'location' => 'Beji, Depok',
            'sessions_count' => 7,
            'savings' => 'Rp 245.000',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Halo Ayu!')
            ->assertSee('7 sesi BARENG')
            ->assertSee('Beji, Depok')
            ->assertSee('Rp 245.000')
            ->assertDontSee('Sedang memuat halaman...');
    }

    public function test_dashboard_displays_three_most_liked_community_posts(): void
    {
        $user = User::factory()->create();

        collect([
            ['caption' => 'Post terbaru, sedikit like', 'likes_count' => 1],
            ['caption' => 'Post populer kedua', 'likes_count' => 8],
            ['caption' => 'Post populer pertama', 'likes_count' => 12],
            ['caption' => 'Post populer ketiga', 'likes_count' => 5],
        ])->map(fn (array $attributes) => Post::create([
            'user_id' => $user->id,
            'image' => 'posts/community.jpg',
            ...$attributes,
        ]));

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('Aktivitas Komunitas')
            ->assertSee('Post populer pertama')
            ->assertSee('Post populer kedua')
            ->assertSee('Post populer ketiga')
            ->assertDontSee('Post terbaru, sedikit like');

        $response->assertSeeInOrder([
            'Post populer pertama',
            'Post populer kedua',
            'Post populer ketiga',
        ]);
    }

    public function test_dashboard_shows_an_empty_state_when_community_has_no_posts(): void
    {
        $user = User::factory()->create(['location' => 'Beji, Depok']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Belum ada postingan komunitas')
            ->assertSee('Belum ada tetangga lain yang terdaftar di lokasi ini.')
            ->assertSee('Belum ada sesi aktif di lokasimu');
    }

    public function test_dashboard_bareng_makan_chart_uses_the_users_participation_in_local_active_sessions(): void
    {
        $viewer = User::factory()->create(['location' => 'Beji, Depok']);
        $host = User::factory()->create(['location' => 'Beji, Depok']);

        $createSession = fn (string $merchant, string $location) => FoodSession::query()->create([
            'host_id' => $host->id,
            'merchant_name' => $merchant,
            'category' => 'Makanan Nusantara',
            'pickup_point' => 'Gerbang utama',
            'location' => $location,
            'status' => 'open',
        ]);

        $joinedSession = $createSession('Nasi Ayam Beji', 'Beji, Depok');
        $createSession('Soto Beji', 'Beji, Depok');
        $createSession('Bakso Bogor', 'Bogor');
        $joinedSession->participants()->create([
            'user_id' => $viewer->id,
            'status' => 'joined',
        ]);

        $this->actingAs($viewer)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('50%')
            ->assertSee('1 dari 2 sesi aktif diikuti');
    }

    public function test_dashboard_neighbors_use_users_in_the_authenticated_users_location(): void
    {
        $viewer = User::factory()->create([
            'name' => 'Ayu Sari',
            'location' => 'Beji, Depok',
        ]);
        $neighborWithPhone = User::factory()->create([
            'name' => 'Lara Wibowo',
            'username' => 'lara',
            'role' => 'Mentor Sesi Makan',
            'location' => ' beji, depok ',
            'phone' => '+62 812-3456',
        ]);
        $neighborWithoutPhone = User::factory()->create([
            'name' => 'Nisa Putri',
            'username' => 'nisa',
            'location' => 'Beji, Depok',
            'phone' => null,
        ]);
        User::factory()->create([
            'name' => 'Budi Bogor',
            'location' => 'Bogor',
        ]);

        $this->actingAs($viewer)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Lara Wibowo')
            ->assertSee('Mentor Sesi Makan')
            ->assertSee('Nisa Putri')
            ->assertDontSee('Budi Bogor')
            ->assertDontSee('Mary Johnson')
            ->assertDontSee('James Brown')
            ->assertSee(route('chat', ['user' => $neighborWithPhone->username]), false)
            ->assertSee('tel:+628123456', false)
            ->assertDontSee('Telepon '.$neighborWithoutPhone->name);
    }
}
