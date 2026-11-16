<?php

namespace Tests\Feature;

use App\Models\Customer\BarengMakanModel as FoodSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BarengMakanTest extends TestCase
{
    use RefreshDatabase;

    public function test_food_page_lists_open_sessions_from_the_users_location(): void
    {
        $viewer = User::factory()->create(['location' => 'Beji, Depok']);
        $localHost = User::factory()->create(['location' => 'Beji, Depok']);
        $otherHost = User::factory()->create(['location' => 'Bogor']);

        $localSession = $this->createSession($localHost, ['merchant_name' => 'Soto Beji']);
        $this->createSession($otherHost, ['merchant_name' => 'Soto Bogor']);
        $this->createSession($localHost, ['merchant_name' => 'Sesi ditutup', 'status' => 'closed']);
        $localSession->participants()->create(['user_id' => $localHost->id, 'status' => 'joined']);

        $this->actingAs($viewer)
            ->get(route('makan'))
            ->assertOk()
            ->assertSee('Soto Beji')
            ->assertDontSee('Soto Bogor')
            ->assertDontSee('Sesi ditutup')
            ->assertSee('Buka sesi makan')
            ->assertSee('food-session-create-dialog');
    }

    public function test_host_can_open_a_persistent_session_and_is_counted_as_a_participant(): void
    {
        $host = User::factory()->create(['location' => 'Beji, Depok']);

        $this->actingAs($host)
            ->post(route('makan.sessions.store'), [
                'merchant_name' => 'Soto Bu Rini',
                'category' => 'Makanan rumahan',
                'description' => 'Pesan sebelum jam makan siang.',
                'pickup_point' => 'Pos satpam Blok C',
                'image_url' => 'https://example.test/warung.jpg',
                'slots_max' => 5,
                'normal_ongkir' => 15000,
                'order_deadline' => now()->addHour()->format('Y-m-d\TH:i'),
                'popular_menus' => "Soto ayam\nEs teh, Perkedel",
            ])
            ->assertRedirect(route('makan'))
            ->assertSessionHas('success', 'Sesi makan berhasil dibuka.');

        $foodSession = FoodSession::query()->where('merchant_name', 'Soto Bu Rini')->firstOrFail();

        $this->assertSame($host->id, $foodSession->host_id);
        $this->assertSame('Beji, Depok', $foodSession->location);
        $this->assertSame('https://example.test/warung.jpg', $foodSession->image);
        $this->assertSame(['Soto ayam', 'Es teh', 'Perkedel'], $foodSession->popular_menus);
        $this->assertDatabaseHas('food_session_participants', [
            'food_session_id' => $foodSession->id,
            'user_id' => $host->id,
            'status' => 'joined',
        ]);
    }

    public function test_neighbor_can_join_a_session_and_host_receives_a_notification(): void
    {
        $host = User::factory()->create(['location' => 'Beji, Depok']);
        $neighbor = User::factory()->create(['location' => 'Beji, Depok']);
        $foodSession = $this->createSession($host, ['slots_max' => 2]);
        $foodSession->participants()->create(['user_id' => $host->id, 'status' => 'joined']);

        $this->actingAs($neighbor)
            ->post(route('makan.sessions.join', $foodSession), [
                'order_items' => '1 nasi ayam, sambal dipisah',
                'estimated_cost' => 25000,
                'notes' => 'Tanpa acar',
            ])
            ->assertRedirect()
            ->assertSessionHas('success', 'Pesananmu sudah masuk ke sesi.');

        $this->assertDatabaseHas('food_session_participants', [
            'food_session_id' => $foodSession->id,
            'user_id' => $neighbor->id,
            'order_items' => '1 nasi ayam, sambal dipisah',
            'estimated_cost' => 25000,
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $host->id,
            'actor_id' => $neighbor->id,
            'type' => 'food_session_join',
        ]);

        $this->actingAs(User::factory()->create(['location' => 'Beji, Depok']))
            ->post(route('makan.sessions.join', $foodSession), [
                'order_items' => '1 es teh',
                'estimated_cost' => 5000,
            ])
            ->assertRedirect()
            ->assertSessionHas('error', 'Slot sesi sudah penuh.');
    }

    public function test_likes_toggle_and_comments_are_saved_with_host_notifications(): void
    {
        $host = User::factory()->create(['location' => 'Beji, Depok']);
        $neighbor = User::factory()->create(['location' => 'Beji, Depok']);
        $foodSession = $this->createSession($host);

        $this->actingAs($neighbor)
            ->post(route('makan.sessions.like', $foodSession))
            ->assertRedirect()
            ->assertSessionHas('success', 'Sesi ditambahkan ke sukamu.');

        $this->assertDatabaseHas('food_session_likes', [
            'food_session_id' => $foodSession->id,
            'user_id' => $neighbor->id,
        ]);

        $this->post(route('makan.sessions.like', $foodSession))
            ->assertRedirect()
            ->assertSessionHas('success', 'Suka dihapus.');

        $this->assertDatabaseMissing('food_session_likes', [
            'food_session_id' => $foodSession->id,
            'user_id' => $neighbor->id,
        ]);

        $this->post(route('makan.sessions.comments.store', $foodSession), [
            'comment' => 'Bisa pesan tanpa sambal?',
        ])
            ->assertRedirect(route('makan.sessions.comments', $foodSession).'#komentar')
            ->assertSessionHas('success', 'Komentar berhasil dikirim.');

        $this->assertDatabaseHas('food_session_comments', [
            'food_session_id' => $foodSession->id,
            'user_id' => $neighbor->id,
            'comment' => 'Bisa pesan tanpa sambal?',
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $host->id,
            'actor_id' => $neighbor->id,
            'type' => 'food_session_like',
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $host->id,
            'actor_id' => $neighbor->id,
            'type' => 'food_session_comment',
        ]);
        $this->assertSame(2, $host->notifications()->count());
    }

    public function test_like_endpoint_returns_updated_state_for_optimistic_updates(): void
    {
        $host = User::factory()->create(['location' => 'Beji, Depok']);
        $neighbor = User::factory()->create(['location' => 'Beji, Depok']);
        $foodSession = $this->createSession($host);

        $this->actingAs($neighbor)
            ->postJson(route('makan.sessions.like', $foodSession))
            ->assertOk()
            ->assertJson([
                'success' => true,
                'liked' => true,
                'likes_count' => 1,
            ]);

        $this->postJson(route('makan.sessions.like', $foodSession))
            ->assertOk()
            ->assertJson([
                'success' => true,
                'liked' => false,
                'likes_count' => 0,
            ]);
    }

    public function test_uploaded_photo_is_stored_and_comments_have_a_separate_page_from_session_details(): void
    {
        Storage::fake('public');
        $host = User::factory()->create(['location' => 'Beji, Depok']);
        $this->actingAs($host);

        $upload = $this->postJson(route('makan.sessions.images.store'), [
            'photo' => UploadedFile::fake()->image('warung.jpg'),
        ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $imagePath = $upload->json('path');
        $this->assertIsString($imagePath);
        Storage::disk('public')->assertExists($imagePath);

        $this->post(route('makan.sessions.store'), [
            'merchant_name' => 'Warung Soto Foto',
            'category' => 'Makanan rumahan',
            'description' => 'Foto dan detail sesi.',
            'pickup_point' => 'Pos satpam',
            'image_path' => $imagePath,
            'slots_max' => 5,
            'normal_ongkir' => 15000,
            'order_deadline' => now()->addHour()->format('Y-m-d\TH:i'),
            'popular_menus' => 'Soto ayam',
        ])->assertRedirect(route('makan'));

        $foodSession = FoodSession::query()->where('merchant_name', 'Warung Soto Foto')->firstOrFail();
        $imageUrl = route('makan.sessions.images.show', ['fileName' => basename($imagePath)]);

        $this->get(route('makan.sessions.images.show', ['fileName' => basename($imagePath)]))
            ->assertOk();
        $this->get(route('makan.sessions.comments', $foodSession))
            ->assertOk()
            ->assertSee('Mulai percakapan')
            ->assertSee('aria-label="Breadcrumb"', false)
            ->assertSee('Komentar')
            ->assertSee('Foto dan detail sesi.')
            ->assertSee($imageUrl);
        $this->get(route('makan'))
            ->assertOk()
            ->assertSee('Lihat detail')
            ->assertSee('food-session-detail-dialog')
            ->assertSee($imageUrl);
    }

    public function test_photo_upload_rejects_non_image_files_with_a_clear_validation_error(): void
    {
        $user = User::factory()->create(['location' => 'Beji, Depok']);

        $this->actingAs($user)
            ->postJson(route('makan.sessions.images.store'), [
                'photo' => UploadedFile::fake()->create('catatan.txt', 10, 'text/plain'),
            ])
            ->assertUnprocessable()
            ->assertJsonPath('errors.photo.0', 'File yang dipilih bukan foto yang valid.');
    }

    private function createSession(User $host, array $attributes = []): FoodSession
    {
        return FoodSession::query()->create(array_merge([
            'host_id' => $host->id,
            'merchant_name' => 'Warung Sore',
            'category' => 'Makanan Nusantara',
            'pickup_point' => 'Pos satpam',
            'location' => $host->location,
            'slots_max' => 5,
            'normal_ongkir' => 15000,
            'order_deadline' => now()->addHour(),
            'popular_menus' => ['Nasi ayam'],
            'status' => 'open',
        ], $attributes));
    }
}
