<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ProfileAndErrorPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_their_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile'))
            ->assertOk();
    }

    public function test_server_errors_use_the_custom_indonesian_error_page(): void
    {
        config(['app.debug' => false]);
        Route::get('/_test-server-error', fn () => abort(500));

        $this->get('/_test-server-error')
            ->assertStatus(500)
            ->assertSee('Terjadi gangguan pada server')
            ->assertSee('Penyebabnya bisa berupa bug aplikasi')
            ->assertDontSee('ErrorException');
    }
}
