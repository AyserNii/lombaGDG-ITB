<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunitySearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_community_search_returns_accounts_matching_name_or_username(): void
    {
        $viewer = User::factory()->create(['name' => 'Viewer']);
        $matchingName = User::factory()->create([
            'name' => 'Nadia Putri',
            'username' => 'nadia_putri',
        ]);
        $matchingUsername = User::factory()->create([
            'name' => 'Rina Sari',
            'username' => 'nadia_rina',
        ]);
        User::factory()->create([
            'name' => 'Budi Santoso',
            'username' => 'budi',
        ]);

        $response = $this->actingAs($viewer)
            ->getJson(route('community.search', ['q' => 'nadia']));

        $response->assertOk()
            ->assertJsonCount(2, 'users')
            ->assertJsonFragment([
                'name' => $matchingName->name,
                'username' => $matchingName->username,
            ])
            ->assertJsonFragment([
                'name' => $matchingUsername->name,
                'username' => $matchingUsername->username,
            ])
            ->assertJsonMissing([
                'name' => 'Budi Santoso',
                'username' => 'budi',
            ]);
    }

    public function test_community_search_returns_posts_matching_their_content(): void
    {
        $viewer = User::factory()->create();
        $matchingPost = Post::create([
            'user_id' => $viewer->id,
            'image' => 'posts/cleaning.jpg',
            'caption' => 'Kerja bakti membersihkan lingkungan',
            'type' => 'kegiatan',
        ]);
        Post::create([
            'user_id' => $viewer->id,
            'image' => 'posts/food.jpg',
            'caption' => 'Berbagi makanan untuk warga',
            'type' => 'kegiatan',
        ]);

        $this->actingAs($viewer)
            ->getJson(route('community.search', ['q' => 'membersihkan']))
            ->assertOk()
            ->assertJsonCount(0, 'users')
            ->assertJsonCount(1, 'posts')
            ->assertJsonFragment([
                'caption' => $matchingPost->caption,
                'post_url' => route('community.posts.show', $matchingPost->id),
            ]);
    }

    public function test_community_search_requires_at_least_two_characters(): void
    {
        $viewer = User::factory()->create();

        $this->actingAs($viewer)
            ->getJson(route('community.search', ['q' => 'a']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('q');
    }

    public function test_community_page_has_an_account_search_launcher_and_empty_search_menu(): void
    {
        $viewer = User::factory()->create();

        $this->actingAs($viewer)
            ->get(route('community'))
            ->assertOk()
            ->assertSee('community-search-launcher', false)
            ->assertSee('community-search-modal', false)
            ->assertSee('Ketik minimal 2 karakter untuk mencari akun atau postingan.')
            ->assertSee('community-search-clear', false);
    }

    public function test_profile_pages_do_not_show_search_inputs(): void
    {
        $viewer = User::factory()->create();

        $this->actingAs($viewer)
            ->get(route('profile'))
            ->assertOk()
            ->assertDontSee('follow-search-input', false)
            ->assertDontSee('Cari sesi, postingan, atau username warga...')
            ->assertSee('id="header-actions" class="ml-auto flex items-center gap-3 md:gap-4"', false);

        $this->actingAs($viewer)
            ->get(route('profile.show', $viewer->id))
            ->assertOk()
            ->assertDontSee('follow-search-input', false)
            ->assertDontSee('Cari sesi, postingan, atau username warga...')
            ->assertSee('id="header-actions" class="ml-auto flex items-center gap-3 md:gap-4"', false);
    }
}
