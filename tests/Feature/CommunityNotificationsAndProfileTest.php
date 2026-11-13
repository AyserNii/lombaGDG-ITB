<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;

class CommunityNotificationsAndProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_and_follow_notification_explain_when_someone_follows_you(): void
    {
        $viewer = User::factory()->create(['username' => 'viewer']);
        $follower = User::factory()->create(['username' => 'follower']);
        $follower->following()->attach($viewer->id);
        Notification::create([
            'user_id' => $viewer->id,
            'actor_id' => $follower->id,
            'type' => 'follow',
            'message' => 'follower started following you.',
        ]);

        $this->actingAs($viewer)
            ->get(route('profile.show', $follower->username))
            ->assertOk()
            ->assertSee('Mengikuti kamu')
            ->assertSee('Follow back');

        $this->get(route('notifications'))
            ->assertOk()
            ->assertSee('Follow back')
            ->assertSee('mulai mengikuti kamu.');

        $this->getJson(route('user.followers', $viewer->id))
            ->assertOk()
            ->assertJsonPath('users.0.follows_you', true);
    }

    public function test_unread_chat_count_is_visible_while_the_sidebar_is_collapsed(): void
    {
        $viewer = User::factory()->create();
        $sender = User::factory()->create();
        Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $viewer->id,
            'message' => 'Ada pesan baru',
            'is_read' => false,
        ]);

        $this->actingAs($viewer)
            ->get(route('profile'))
            ->assertOk()
            ->assertSee('sidebar-unread-badge', false)
            ->assertSee('1 pesan belum dibaca')
            ->assertDontSee('sidebar-label mr-2 flex h-5 min-w-[20px]');
    }

    public function test_opening_notification_dropdown_marks_notifications_read_without_removing_them(): void
    {
        $viewer = User::factory()->create();
        $actor = User::factory()->create();
        $notification = Notification::create([
            'user_id' => $viewer->id,
            'actor_id' => $actor->id,
            'type' => 'follow',
            'message' => 'warga started following you.',
            'is_read' => false,
        ]);

        $this->actingAs($viewer)
            ->getJson(route('notifications.dropdown'))
            ->assertOk()
            ->assertJsonPath('notifications.0.id', $notification->id)
            ->assertJsonPath('notifications.0.is_read', true)
            ->assertJsonPath('unread_count', 0);

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'is_read' => true,
        ]);
    }

    public function test_following_notification_filter_only_shows_actors_the_user_follows(): void
    {
        $viewer = User::factory()->create();
        $followedActor = User::factory()->create();
        $otherActor = User::factory()->create();
        $viewer->following()->attach($followedActor->id);

        Notification::create([
            'user_id' => $viewer->id,
            'actor_id' => $followedActor->id,
            'type' => 'follow',
            'message' => 'followed actor notification',
        ]);
        Notification::create([
            'user_id' => $viewer->id,
            'actor_id' => $otherActor->id,
            'type' => 'follow',
            'message' => 'other actor notification',
        ]);

        $this->actingAs($viewer)
            ->getJson(route('notifications', ['filter' => 'following']))
            ->assertOk()
            ->assertJsonCount(1, 'notifications')
            ->assertJsonPath('notifications.0.actor_id', $followedActor->id);
    }

    public function test_delete_all_notifications_only_removes_the_authenticated_users_notifications(): void
    {
        $viewer = User::factory()->create();
        $otherUser = User::factory()->create();
        $actor = User::factory()->create();
        $ownedNotification = Notification::create([
            'user_id' => $viewer->id,
            'actor_id' => $actor->id,
            'type' => 'follow',
            'message' => 'warga started following you.',
        ]);
        $otherNotification = Notification::create([
            'user_id' => $otherUser->id,
            'actor_id' => $actor->id,
            'type' => 'follow',
            'message' => 'warga started following you.',
        ]);

        $this->actingAs($viewer)
            ->deleteJson(route('notifications.destroy-all'))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('deleted_count', 1);

        $this->assertDatabaseMissing('notifications', ['id' => $ownedNotification->id]);
        $this->assertDatabaseHas('notifications', ['id' => $otherNotification->id]);
    }

    public function test_like_and_comment_notifications_show_the_post_and_comment(): void
    {
        $owner = User::factory()->create();
        $actor = User::factory()->create();
        $post = Post::create([
            'user_id' => $owner->id,
            'image' => 'posts/warga.jpg',
            'caption' => 'Kerja bakti lingkungan',
        ]);
        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $actor->id,
            'body' => 'Aku ikut bantu besok pagi!',
        ]);

        Notification::create([
            'user_id' => $owner->id,
            'actor_id' => $actor->id,
            'type' => 'like',
            'message' => 'warga menyukai postingan Anda.',
            'post_id' => $post->id,
        ]);
        Notification::create([
            'user_id' => $owner->id,
            'actor_id' => $actor->id,
            'type' => 'comment',
            'message' => '@warga mengomentari postingan Anda.',
            'post_id' => $post->id,
            'comment_id' => $comment->id,
        ]);

        $this->actingAs($owner)
            ->get(route('notifications'))
            ->assertOk()
            ->assertSee('menyukai postingan kamu.')
            ->assertSee('Postingan: Kerja bakti lingkungan')
            ->assertSee('Komentar: Aku ikut bantu besok pagi!');
    }

    public function test_post_reactions_can_switch_between_like_dislike_and_none(): void
    {
        $owner = User::factory()->create();
        $reactor = User::factory()->create();
        $post = Post::create([
            'user_id' => $owner->id,
            'image' => 'posts/warga.jpg',
            'caption' => 'Menguji reaksi',
        ]);

        $this->actingAs($reactor)
            ->postJson(route('posts.like', $post->id), ['reaction' => 'like'])
            ->assertOk()
            ->assertJsonPath('reaction', 'like')
            ->assertJsonPath('likes_count', 1)
            ->assertJsonPath('dislikes_count', 0);

        $this->postJson(route('posts.like', $post->id), ['reaction' => 'dislike'])
            ->assertOk()
            ->assertJsonPath('reaction', 'dislike')
            ->assertJsonPath('likes_count', 0)
            ->assertJsonPath('dislikes_count', 1);

        $this->postJson(route('posts.like', $post->id), ['reaction' => 'dislike'])
            ->assertOk()
            ->assertJsonPath('reaction', null)
            ->assertJsonPath('likes_count', 0)
            ->assertJsonPath('dislikes_count', 0);

        $this->assertDatabaseMissing('post_likes', [
            'post_id' => $post->id,
            'user_id' => $reactor->id,
        ]);
    }

    public function test_community_comment_link_opens_a_dedicated_discussion_page(): void
    {
        $author = User::factory()->create();
        $commenter = User::factory()->create();
        $post = Post::create([
            'user_id' => $author->id,
            'image' => 'posts/warga.jpg',
            'caption' => 'Diskusi kerja bakti',
        ]);
        Comment::create([
            'post_id' => $post->id,
            'user_id' => $commenter->id,
            'body' => 'Saya ikut membantu!',
        ]);

        $this->actingAs($author)
            ->get(route('community'))
            ->assertOk()
            ->assertSee(route('community.posts.show', $post->id).'#komentar', false)
            ->assertDontSee('Â·', false);

        $this->get(route('community.posts.show', ['id' => $post->id]).'#komentar')
            ->assertOk()
            ->assertSee('Tulis Tanggapan Anda')
            ->assertSee('Saya ikut membantu!')
            ->assertSee('Teratas')
            ->assertSee('Terbaru')
            ->assertSee('Terlama');

        $this->get(route('profile'))
            ->assertOk()
            ->assertSee('openPostDetailModal');
    }

    public function test_replacing_a_profile_photo_removes_only_the_previous_local_avatar(): void
    {
        $oldAvatarPath = public_path('uploads/avatars/avatar_old_for_test.png');
        File::ensureDirectoryExists(dirname($oldAvatarPath));
        File::put($oldAvatarPath, 'old avatar');
        $user = User::factory()->create(['avatar' => '/uploads/avatars/avatar_old_for_test.png']);

        $this->actingAs($user)->post(route('settings.update'), [
            'name' => $user->name,
            'gender' => 'prefer_not_to_say',
            'location_country' => 'Indonesia',
            'location' => 'Beji, Depok',
            'avatar_cropped' => 'data:image/png;base64,'.base64_encode('new avatar'),
        ])->assertRedirect();

        $this->assertFileDoesNotExist($oldAvatarPath);
        $newAvatarPath = public_path($user->fresh()->avatar);
        $this->assertFileExists($newAvatarPath);
        File::delete($newAvatarPath);
    }

    public function test_user_can_upload_replace_and_remove_profile_banner(): void
    {
        $oldCoverPath = public_path('uploads/profile-covers/cover_old_for_test.png');
        File::ensureDirectoryExists(dirname($oldCoverPath));
        File::put($oldCoverPath, 'old cover');
        $user = User::factory()->create(['profile_cover' => '/uploads/profile-covers/cover_old_for_test.png']);

        $this->actingAs($user)->post(route('settings.update'), [
            'name' => $user->name,
            'gender' => 'prefer_not_to_say',
            'location_country' => 'Indonesia',
            'location' => 'Beji, Depok',
            'profile_cover_file' => UploadedFile::fake()->image('new-cover.jpg', 1400, 500),
        ])->assertRedirect();

        $this->assertFileDoesNotExist($oldCoverPath);
        $newCover = $user->fresh()->profile_cover;
        $newCoverPath = public_path($newCover);
        $this->assertFileExists($newCoverPath);

        $this->get(route('profile'))
            ->assertOk()
            ->assertSee('Background profil '.$user->name)
            ->assertSee($newCover, false);

        $this->actingAs($user)->post(route('settings.update'), [
            'name' => $user->name,
            'gender' => 'prefer_not_to_say',
            'location_country' => 'Indonesia',
            'location' => 'Beji, Depok',
            'remove_profile_cover' => '1',
        ])->assertRedirect();

        $this->assertNull($user->fresh()->profile_cover);
        $this->assertFileDoesNotExist($newCoverPath);
    }

    public function test_incoming_chat_message_creates_a_chat_notification(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $this->actingAs($sender)
            ->postJson(route('chat.send'), [
                'receiver_id' => $receiver->id,
                'message' => 'Kamu jadi ikut kerja bakti?',
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $receiver->id,
            'actor_id' => $sender->id,
            'type' => 'message',
        ]);

        $this->actingAs($receiver)
            ->get(route('notifications'))
            ->assertOk()
            ->assertSee('mengirim pesan: "Kamu jadi ikut kerja bakti?"')
            ->assertSee('Balas');
    }

    public function test_chat_sync_returns_only_new_messages_and_unseen_read_receipts_after_initial_load(): void
    {
        $viewer = User::factory()->create();
        $contact = User::factory()->create();
        $otherUser = User::factory()->create();

        $outgoingMessage = Message::create([
            'sender_id' => $viewer->id,
            'receiver_id' => $contact->id,
            'message' => 'Pesan terkirim',
            'is_read' => false,
        ]);
        $incomingMessage = Message::create([
            'sender_id' => $contact->id,
            'receiver_id' => $viewer->id,
            'message' => 'Pesan masuk',
            'is_read' => false,
        ]);
        Message::create([
            'sender_id' => $otherUser->id,
            'receiver_id' => $viewer->id,
            'message' => 'Percakapan lain',
            'is_read' => false,
        ]);

        $initialSync = $this->actingAs($viewer)
            ->getJson(route('chat.sync', ['receiver_id' => $contact->id]))
            ->assertOk()
            ->assertJsonCount(2, 'messages')
            ->assertJsonPath('messages.1.id', $incomingMessage->id)
            ->assertJsonPath('last_message_id', $incomingMessage->id)
            ->assertJsonPath('read_ids', []);

        $outgoingMessage->update(['is_read' => true]);
        $newIncomingMessage = Message::create([
            'sender_id' => $contact->id,
            'receiver_id' => $viewer->id,
            'message' => 'Pesan berikutnya',
            'is_read' => false,
        ]);

        $this->getJson(route('chat.sync', [
            'receiver_id' => $contact->id,
            'after_id' => $initialSync->json('last_message_id'),
            'read_after_id' => $initialSync->json('last_read_message_id'),
        ]))
            ->assertOk()
            ->assertJsonCount(1, 'messages')
            ->assertJsonPath('messages.0.id', $newIncomingMessage->id)
            ->assertJsonPath('read_ids.0', $outgoingMessage->id);
    }

    public function test_profile_completion_reaches_one_hundred_only_when_profile_fields_are_filled(): void
    {
        $user = User::factory()->create([
            'username' => 'warga',
            'headline' => 'Tetangga aktif',
            'avatar' => '/uploads/avatars/warga.png',
            'bio' => 'Siap gotong royong.',
            'location' => 'Margonda, Depok',
            'phone' => '08123456789',
            'website' => 'https://bareng.test',
            'gender' => 'prefer_not_to_say',
        ]);

        $completeResponse = $this->actingAs($user)->get(route('settings'))->assertOk();
        $this->assertSame(100, $this->profileCompletionPercentage($completeResponse));

        $user->update(['phone' => null]);

        $incompleteResponse = $this->get(route('settings'))->assertOk();
        $this->assertSame(89, $this->profileCompletionPercentage($incompleteResponse));
    }

    public function test_registration_location_has_country_suggestions_and_gps_without_a_map(): void
    {
        $this->get(route('auth.register'))
            ->assertOk()
            ->assertSee('Ketik atau pilih negara')
            ->assertSee('ketik kota atau wilayah')
            ->assertSee('Indonesia')
            ->assertSee('Gunakan lokasi GPS')
            ->assertSee('location_latitude', false)
            ->assertSee('photon.komoot.io')
            ->assertDontSee('leaflet')
            ->assertDontSee('Pilih di peta');
    }

    public function test_registration_saves_the_selected_location_and_coordinates(): void
    {
        $this->post(route('auth.store'), [
            'name' => 'Warga Baru',
            'email' => 'warga@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
            'location_country' => 'Indonesia',
            'location' => 'Margonda, Depok',
            'location_latitude' => '-6.389',
            'location_longitude' => '106.83',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'warga@example.test',
            'location_country' => 'Indonesia',
            'location' => 'Margonda, Depok',
            'location_latitude' => '-6.3890000',
            'location_longitude' => '106.8300000',
        ]);
    }

    public function test_settings_save_the_updated_location_coordinates(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('settings.update'), [
                'name' => $user->name,
                'gender' => 'prefer_not_to_say',
                'location_country' => 'Indonesia',
                'location' => 'Beji, Depok',
                'location_latitude' => '-6.368',
                'location_longitude' => '106.83',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'location_country' => 'Indonesia',
            'location' => 'Beji, Depok',
            'location_latitude' => '-6.3680000',
            'location_longitude' => '106.8300000',
            'role' => 'Customer · Beji, Depok',
        ]);
    }

    public function test_location_repair_migration_restores_missing_columns_idempotently(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['location_latitude', 'location_longitude', 'location_country']);
        });

        $migration = require database_path('migrations/2026_11_11_000001_repair_user_location_columns.php');
        $migration->up();
        $migration->up();

        $this->assertTrue(Schema::hasColumn('users', 'location_latitude'));
        $this->assertTrue(Schema::hasColumn('users', 'location_longitude'));
        $this->assertTrue(Schema::hasColumn('users', 'location_country'));
    }

    public function test_google_profile_photo_is_cached_locally_for_new_accounts(): void
    {
        $googleId = (string) Str::uuid();
        $avatarUrl = 'https://lh3.googleusercontent.com/a/test-photo=s96-c';
        $googleUser = new class($googleId, $avatarUrl) implements SocialiteUser
        {
            public function __construct(private string $id, private string $avatar) {}

            public function getId()
            {
                return $this->id;
            }

            public function getNickname()
            {
                return null;
            }

            public function getName()
            {
                return 'Warga Google';
            }

            public function getEmail()
            {
                return 'google-warga@example.test';
            }

            public function getAvatar()
            {
                return $this->avatar;
            }
        };
        $provider = new class($googleUser)
        {
            public function __construct(private SocialiteUser $user) {}

            public function stateless()
            {
                return $this;
            }

            public function user(): SocialiteUser
            {
                return $this->user;
            }
        };
        $filename = 'google_'.hash('sha256', $googleId).'.jpg';
        $avatarPath = public_path('uploads/avatars/'.$filename);
        Http::fake([
            'lh3.googleusercontent.com/*' => Http::response('profile-image', 200, ['Content-Type' => 'image/jpeg']),
        ]);
        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        try {
            $this->get(route('auth.google.callback'))
                ->assertRedirect(route('settings'));

            $this->assertDatabaseHas('users', [
                'email' => 'google-warga@example.test',
                'avatar' => '/uploads/avatars/'.$filename,
                'location' => 'Belum diatur',
            ]);
            $this->assertFileExists($avatarPath);
            Http::assertSent(fn ($request) => $request->url() === $avatarUrl);
        } finally {
            if (File::exists($avatarPath)) {
                File::delete($avatarPath);
            }
        }
    }

    private function profileCompletionPercentage(TestResponse $response): int
    {
        preg_match(
            '/<h2[^>]*>Kelengkapan Profil<\/h2>.*?<span[^>]*>\s*(\d+)%/s',
            $response->getContent(),
            $matches
        );

        $this->assertArrayHasKey(1, $matches, 'The rendered profile completion percentage is missing.');

        return (int) $matches[1];
    }
}
