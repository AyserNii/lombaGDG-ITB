<?php

namespace Tests\Feature;

use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_a_story_that_expires_after_24_hours(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('stories.store'), [
            'image' => UploadedFile::fake()->image('moment.jpg'),
            'caption' => 'Momen warga hari ini',
        ]);

        $response->assertRedirect(route('community'));
        $this->assertDatabaseCount('stories', 1);

        $story = Story::firstOrFail();
        $this->assertSame('Momen warga hari ini', $story->caption);
        $this->assertTrue($story->expires_at->between(now()->addHours(23), now()->addHours(25)));
        Storage::disk('public')->assertExists($story->image);
    }

    public function test_user_can_add_only_their_own_stories_to_a_highlight(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $story = Story::create([
            'user_id' => $owner->id,
            'image' => 'stories/moment.jpg',
            'caption' => 'Momen',
            'expires_at' => now()->addDay(),
        ]);

        $this->actingAs($owner)->post(route('stories.highlights.store'), [
            'title' => 'Kenangan',
            'story_ids' => [$story->id],
        ])->assertRedirect();

        $this->assertDatabaseHas('story_highlights', [
            'user_id' => $owner->id,
            'title' => 'Kenangan',
        ]);
        $this->assertDatabaseHas('story_highlight_stories', [
            'story_id' => $story->id,
        ]);

        $otherStory = Story::create([
            'user_id' => $otherUser->id,
            'image' => 'stories/other.jpg',
            'caption' => 'Bukan milikmu',
            'expires_at' => now()->addDay(),
        ]);

        $this->from(route('profile'))->actingAs($owner)->post(route('stories.highlights.store'), [
            'title' => 'Tidak valid',
            'story_ids' => [$otherStory->id],
        ])->assertRedirect(route('profile'))->assertSessionHasErrors('story_ids');

        $this->assertDatabaseMissing('story_highlights', [
            'user_id' => $owner->id,
            'title' => 'Tidak valid',
        ]);
    }

    public function test_community_profile_and_chat_render_without_seeded_post_content(): void
    {
        $user = User::factory()->create();
        $contact = User::factory()->create();

        $this->actingAs($user)
            ->get(route('community'))
            ->assertOk()
            ->assertSee('Cerita Anda')
            ->assertSee('Belum ada postingan')
            ->assertSee('Dashboard')
            ->assertDontSee('Cari sesi, postingan, atau username warga');

        $this->actingAs($user)
            ->get(route('profile'))
            ->assertOk()
            ->assertSee('Buat sorotan');

        $this->actingAs($user)
            ->get(route('chat', ['user' => $contact->id]))
            ->assertOk()
            ->assertSee('bg-white', false)
            ->assertSee('Dashboard')
            ->assertSee('Messages')
            ->assertDontSee('Cari sesi, postingan, atau username warga');
    }
}
