<?php

namespace Tests\Feature;

use App\Models\Post;
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
            ->assertSee('Rp 245.000');
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
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Belum ada postingan komunitas');
    }
}
