<?php

use App\Http\Controllers\Auth\GoogleAuthController as AuthGoogle;
use App\Http\Controllers\Auth\LoginController as AuthLogin;
use App\Http\Controllers\Auth\RegisterController as AuthRegister;
use App\Http\Controllers\Customer\BarengBelanjaController as CustomerBarengBelanja;
use App\Http\Controllers\Customer\BarengKirimController as CustomerBarengKirim;
use App\Http\Controllers\Customer\BarengMakanController as CustomerBarengMakan;
use App\Http\Controllers\Customer\BerandaController as CustomerBeranda;
use App\Http\Controllers\Customer\ChatController as CustomerChat;
use App\Http\Controllers\Customer\KomunitasController as CustomerKomunitas;
use App\Http\Controllers\Customer\NotificationController as CustomerNotification;
use App\Http\Controllers\Customer\PengaturanController as CustomerPengaturan;
use App\Http\Controllers\Customer\PostController as CustomerPost;
use App\Http\Controllers\Customer\ProfileController as CustomerProfile;
use App\Http\Controllers\Customer\SesiAktifController as CustomerSesiAktif;
use App\Http\Controllers\Customer\StoryController as CustomerStory;
use Illuminate\Support\Facades\Route;

Route::fallback(function () {
    if (request()->method() === 'GET') {
        abort(404);
    }
    abort(404);
});

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// ─── AUTHENTICATION (PREFIX & GROUP STRUKTUR SESUAI CONTOH) ─────────────────────
Route::prefix('auth')->name('auth.')->group(function () {
    Route::middleware(['guest'])->group(function () {
        Route::get('/login', [AuthLogin::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthLogin::class, 'authenticate'])->name('authenticate');
        Route::get('/register', [AuthRegister::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [AuthRegister::class, 'register'])->name('store');

        // Google OAuth
        Route::get('/google', [AuthGoogle::class, 'redirectToGoogle'])->name('google');
        Route::get('/google/callback', [AuthGoogle::class, 'handleGoogleCallback'])->name('google.callback');
    });

    Route::post('/logout', [AuthLogin::class, 'logout'])->name('logout');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthLogin::class, 'showLoginForm'])->name('login');
    Route::get('/register', [AuthRegister::class, 'showRegistrationForm'])->name('register');
});
Route::post('/logout', [AuthLogin::class, 'logout'])->name('logout');

// ─── CUSTOMER ROUTES (SESUAI POLA STRUKTUR CONTOH) ──────────────────────────────
Route::prefix('customer')->name('customer.')->group(function () {
    Route::middleware(['auth'])->group(function () {
        // Beranda / Dashboard
        Route::prefix('beranda')->name('beranda.')->group(function () {
            Route::get('/', [CustomerBeranda::class, 'index'])->name('index');
            Route::get('/list', [CustomerBeranda::class, 'index'])->name('list');
        });

        // Bareng Makan
        Route::prefix('makan')->name('makan.')->group(function () {
            Route::get('/', [CustomerBarengMakan::class, 'list'])->name('list');
            Route::get('/hal/{halaman}', [CustomerBarengMakan::class, 'list'])->name('halaman');
            Route::get('/index', [CustomerBarengMakan::class, 'index'])->name('index');
        });

        // Bareng Belanja
        Route::prefix('belanja')->name('belanja.')->group(function () {
            Route::get('/', [CustomerBarengBelanja::class, 'index'])->name('list');
            Route::get('/hal/{halaman}', [CustomerBarengBelanja::class, 'index'])->name('halaman');
            Route::get('/index', [CustomerBarengBelanja::class, 'index'])->name('index');
        });

        // Bareng Kirim
        Route::prefix('kirim')->name('kirim.')->group(function () {
            Route::get('/', [CustomerBarengKirim::class, 'index'])->name('list');
            Route::get('/hal/{halaman}', [CustomerBarengKirim::class, 'index'])->name('halaman');
            Route::get('/index', [CustomerBarengKirim::class, 'index'])->name('index');
        });

        // Sesi Aktif
        Route::prefix('sesi')->name('sesi.')->group(function () {
            Route::get('/', [CustomerSesiAktif::class, 'index'])->name('list');
            Route::get('/hal/{halaman}', [CustomerSesiAktif::class, 'index'])->name('halaman');
            Route::get('/index', [CustomerSesiAktif::class, 'index'])->name('index');
        });

        // Komunitas
        Route::prefix('komunitas')->name('komunitas.')->group(function () {
            Route::get('/', [CustomerKomunitas::class, 'index'])->name('list');
            Route::get('/hal/{halaman}', [CustomerKomunitas::class, 'index'])->name('halaman');
            Route::get('/index', [CustomerKomunitas::class, 'index'])->name('index');
        });

        // Postingan
        Route::prefix('posts')->name('posts.')->group(function () {
            Route::get('/', [CustomerPost::class, 'list'])->name('list');
            Route::get('/hal/{halaman}', [CustomerPost::class, 'list'])->name('halaman');
            Route::post('/caridata', [CustomerPost::class, 'caridata'])->name('caridata');
            Route::get('/cari/{cari}', [CustomerPost::class, 'cari'])->name('cari');
            Route::get('/cari/{cari}/hal/{halaman}', [CustomerPost::class, 'cari'])->name('carihalaman');
            Route::get('/detail/{id}', [CustomerPost::class, 'show'])->name('detail');
            Route::post('/hapusdata/{id}', [CustomerPost::class, 'destroy'])->name('hapusdata');
            Route::post('/like/{id}', [CustomerPost::class, 'toggleLike'])->name('like');
        });

        // Profil
        Route::prefix('profil')->name('profil.')->group(function () {
            Route::get('/', [CustomerProfile::class, 'index'])->name('list');
            Route::get('/detail/{username}', [CustomerProfile::class, 'show'])->name('detail');
            Route::post('/note', [CustomerProfile::class, 'updateNote'])->name('note');
        });

        // Pengaturan
        Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
            Route::get('/', [CustomerPengaturan::class, 'index'])->name('list');
            Route::get('/index', [CustomerPengaturan::class, 'index'])->name('index');
            Route::post('/perbaruidata', [CustomerPengaturan::class, 'update'])->name('perbaruidata');
            Route::post('/', [CustomerPengaturan::class, 'update'])->name('update');
        });

        // Chat
        Route::prefix('chat')->name('chat.')->group(function () {
            Route::get('/', [CustomerChat::class, 'index'])->name('list');
            Route::post('/kirimpesan', [CustomerChat::class, 'send'])->name('kirimpesan');
        });

        // Notifikasi
        Route::prefix('notifikasi')->name('notifikasi.')->group(function () {
            Route::get('/', [CustomerNotification::class, 'index'])->name('list');
            Route::get('/dropdown', [CustomerNotification::class, 'dropdown'])->name('dropdown');
            Route::get('/unread-count', [CustomerNotification::class, 'count'])->name('unread-count');
        });
    });
});

// ─── FLAT ROUTE COMPATIBILITY (UNTUK BLADE VIEWS & JS AJAX SEBELUMNYA) ──────────
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [CustomerBeranda::class, 'index'])->name('dashboard');
    Route::get('/makan', [CustomerBarengMakan::class, 'index'])->name('makan');
    Route::get('/belanja', [CustomerBarengBelanja::class, 'index'])->name('belanja');
    Route::get('/kirim', [CustomerBarengKirim::class, 'index'])->name('kirim');
    Route::get('/sesi', [CustomerSesiAktif::class, 'index'])->name('sesi');
    Route::get('/komunitas', [CustomerKomunitas::class, 'index'])->name('community');
    Route::get('/community/posts/{id}', [CustomerPost::class, 'communityShow'])->name('community.posts.show');
    Route::get('/pengaturan', [CustomerPengaturan::class, 'index'])->name('settings');
    Route::post('/pengaturan', [CustomerPengaturan::class, 'update'])->name('settings.update');

    Route::get('/profile', [CustomerProfile::class, 'index'])->name('profile');
    Route::get('/profile/{username}', [CustomerProfile::class, 'show'])->name('profile.show');
    Route::post('/profile/note', [CustomerProfile::class, 'updateNote'])->name('profile.note');
    Route::post('/users/{id}/toggle-follow', [CustomerProfile::class, 'toggleFollow'])->name('user.toggle-follow');
    Route::get('/users/{id}/followers', [CustomerProfile::class, 'getFollowers'])->name('user.followers');
    Route::get('/users/{id}/following', [CustomerProfile::class, 'getFollowing'])->name('user.following');
    Route::post('/posts/{id}/toggle-save', [CustomerProfile::class, 'toggleSave'])->name('post.toggle-save');
    Route::post('/posts', [CustomerProfile::class, 'storePost'])->name('posts.store');
    Route::get('/posts/{id}', [CustomerPost::class, 'show'])->name('posts.show');
    Route::delete('/posts/{id}', [CustomerPost::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{id}/like', [CustomerPost::class, 'toggleLike'])->name('posts.like');
    Route::get('/posts/{id}/comments', [CustomerPost::class, 'getComments'])->name('posts.comments');
    Route::post('/posts/{id}/comments', [CustomerPost::class, 'storeComment'])->name('posts.comments.store');
    Route::delete('/comments/{id}', [CustomerPost::class, 'destroyComment'])->name('comments.destroy');

    Route::get('/chat', [CustomerChat::class, 'index'])->name('chat');
    Route::get('/chat/sync', [CustomerChat::class, 'sync'])->name('chat.sync');
    Route::post('/chat/send', [CustomerChat::class, 'send'])->name('chat.send');
    Route::post('/stories', [CustomerStory::class, 'store'])->name('stories.store');
    Route::get('/stories/{story}/image', [CustomerStory::class, 'image'])->name('stories.image');
    Route::get('/story-highlights/{highlight}/image', [CustomerStory::class, 'highlightImage'])->name('stories.highlights.image');
    Route::post('/stories/highlights', [CustomerStory::class, 'storeHighlight'])->name('stories.highlights.store');

    Route::get('/notifications', [CustomerNotification::class, 'index'])->name('notifications');
    Route::get('/notifications/dropdown', [CustomerNotification::class, 'dropdown'])->name('notifications.dropdown');
    Route::get('/notifications/unread-count', [CustomerNotification::class, 'count'])->name('notifications.count');
    Route::delete('/notifications', [CustomerNotification::class, 'destroyAll'])->name('notifications.destroy-all');
});
