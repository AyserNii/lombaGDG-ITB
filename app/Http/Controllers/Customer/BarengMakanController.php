<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\BarengMakanModel as MainModel;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

header('Cache-Control: no-store, private, no-cache, must-revalidate');
header('Cache-Control: pre-check=0, post-check=0, max-age=0, max-stale = 0', false);
header('Pragma: public');
header('Pragma: no-cache');

class BarengMakanController extends Controller
{
    private $role = 'customer';

    private $module = 'gotongroyong';

    private $page = 'makan';

    private $title = 'Bareng Makan';

    private $sessionNamespace;

    private $basePath;

    private $routePrefix;

    private $routeName;

    private $lastPageKey;

    private $alertKey;

    public function __construct()
    {
        $this->routeName = '/'.$this->page;
        $this->basePath = $this->role.'.'.$this->module.'.';
        $this->routePrefix = 'makan.sessions.';
        $this->alertKey = 'notifikasi'.$this->role.$this->module;
        $this->lastPageKey = 'url'.$this->role.$this->module.$this->page;
        $this->sessionNamespace = 'sesi'.$this->role.$this->module;
    }

    public function list(Request $request, $halaman = null)
    {
        if ($halaman !== null && (! ctype_digit((string) $halaman) || (int) $halaman < 1)) {
            return redirect()->route($this->page);
        }

        $user = Auth::user();
        $location = trim((string) $user->location);
        $searchTerm = trim((string) $request->query('q', ''));

        $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $browseData = MainModel::browse(
            $location,
            $searchTerm,
            $user->id,
            max(1, (int) ($halaman ?? 1))
        );

        $datakonten['user'] = $user;
        $datakonten['activeSessions'] = $browseData['sessions'];
        $datakonten['activeSessionCount'] = $browseData['activeSessionCount'];
        $datakonten['activeParticipantCount'] = $browseData['activeParticipantCount'];
        $datakonten['searchTerm'] = $searchTerm;
        $datakonten['locationLabel'] = $location !== '' ? $location : 'lokasi sekitar';
        $datakonten['canHostSession'] = $location !== '';

        return view('customer.makan.index', $datakonten);
    }

    public function commentsPage(MainModel $foodSession)
    {
        $foodSession->loadHost();

        $datakonten['sesi'] = $foodSession;
        $datakonten['comments'] = $foodSession->paginateComments();

        return view('customer.makan.comments', $datakonten);
    }

    public function index()
    {
        return $this->list(request());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'merchant_name' => ['required', 'string', 'max:120'],
            'category' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:1000'],
            'pickup_point' => ['required', 'string', 'max:160'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'image_path' => ['nullable', 'string', 'regex:/\Afood-sessions\/[A-Za-z0-9._-]+\z/', 'max:255'],
            'slots_max' => ['required', 'integer', 'min:2', 'max:20'],
            'normal_ongkir' => ['required', 'integer', 'min:0', 'max:1000000'],
            'order_deadline' => ['required', 'date', 'after:now'],
            'popular_menus' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! empty($validated['image_path']) && ! Storage::disk('public')->fileExists($validated['image_path'])) {
            return back()
                ->withInput()
                ->withErrors(['image_path' => 'Foto yang diunggah sudah tidak tersedia. Silakan unggah ulang.']);
        }

        $user = Auth::user();
        $location = trim((string) $user->location);

        if ($location === '') {
            return back()
                ->withInput()
                ->with('error', 'Lengkapi lokasi profil sebelum membuka sesi makan.');
        }

        $popularMenus = collect(preg_split('/[\r\n,]+/', $validated['popular_menus'] ?? ''))
            ->map(fn (string $menu) => trim($menu))
            ->filter()
            ->unique()
            ->take(8)
            ->values()
            ->all();

        MainModel::createForHost($user, [
            'merchant_name' => $validated['merchant_name'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'pickup_point' => $validated['pickup_point'],
            'location' => $location,
            'image' => $validated['image_path'] ?? $validated['image_url'] ?? null,
            'slots_max' => $validated['slots_max'],
            'normal_ongkir' => $validated['normal_ongkir'],
            'order_deadline' => $validated['order_deadline'],
            'popular_menus' => $popularMenus,
            'status' => 'open',
        ]);

        return redirect()->route('makan')->with('success', 'Sesi makan berhasil dibuka.');
    }

    public function uploadImage(Request $request)
    {
        $validated = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'photo.required' => 'Pilih foto sebelum mengunggah.',
            'photo.image' => 'File yang dipilih bukan foto yang valid.',
            'photo.mimes' => 'Format foto harus JPG, PNG, atau WEBP.',
            'photo.max' => 'Ukuran foto maksimal 5 MB.',
        ]);

        $path = $validated['photo']->store('food-sessions', 'public');
        if (! is_string($path) || $path === '') {
            return response()->json([
                'success' => false,
                'message' => 'Server tidak dapat menyimpan foto. Periksa koneksi lalu coba lagi.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => route('makan.sessions.images.show', ['fileName' => basename($path)]),
        ]);
    }

    public function image(string $fileName)
    {
        abort_unless(
            basename($fileName) === $fileName
                && preg_match('/\A[a-z0-9][a-z0-9._-]*\.(?:jpe?g|png|webp)\z/i', $fileName) === 1,
            404
        );

        $path = 'food-sessions/'.$fileName;
        abort_unless(Storage::disk('public')->fileExists($path), 404);

        return Storage::disk('public')->response($path);
    }

    public function join(Request $request, MainModel $foodSession): RedirectResponse
    {
        $validated = $request->validate([
            'order_items' => ['required', 'string', 'max:500'],
            'estimated_cost' => ['required', 'integer', 'min:0', 'max:10000000'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $user = Auth::user();
        $result = DB::transaction(function () use ($foodSession, $validated, $user): array {
            $lockedSession = $foodSession->lockForUpdate();

            if ($lockedSession->status !== 'open' || ($lockedSession->order_deadline && $lockedSession->order_deadline->isPast())) {
                if ($lockedSession->status === 'open') {
                    $lockedSession->close();
                }

                return ['joined' => false, 'message' => 'Sesi ini sudah ditutup.'];
            }

            if ($lockedSession->hasJoined($user->id)) {
                return ['joined' => false, 'message' => 'Kamu sudah terdaftar di sesi ini.'];
            }

            if ($lockedSession->joinedParticipantCount() >= $lockedSession->slots_max) {
                return ['joined' => false, 'message' => 'Slot sesi sudah penuh.'];
            }

            $lockedSession->addParticipant($user->id, [
                'order_items' => $validated['order_items'],
                'estimated_cost' => $validated['estimated_cost'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $this->notifySessionHost(
                $lockedSession,
                $user,
                'food_session_join',
                $user->name.' bergabung ke sesi makan '.$lockedSession->merchant_name.'.'
            );

            return ['joined' => true, 'message' => null];
        });

        if (! $result['joined']) {
            return back()->withInput()->with('error', $result['message']);
        }

        return back()->with('success', 'Pesananmu sudah masuk ke sesi.');
    }

    public function toggleLike(Request $request, MainModel $foodSession): RedirectResponse|JsonResponse
    {
        $user = Auth::user();
        $liked = DB::transaction(function () use ($foodSession, $user): bool {
            $lockedSession = $foodSession->lockForUpdate();
            $liked = $lockedSession->toggleLikeFor($user->id);

            if ($liked) {
                $this->notifySessionHost(
                    $lockedSession,
                    $user,
                    'food_session_like',
                    $user->name.' menyukai sesi makan '.$lockedSession->merchant_name.'.'
                );
            }

            return $liked;
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likes_count' => $foodSession->likesCount(),
            ]);
        }

        return back()->with('success', $liked ? 'Sesi ditambahkan ke sukamu.' : 'Suka dihapus.');
    }

    public function comment(Request $request, MainModel $foodSession): RedirectResponse
    {
        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        $user = Auth::user();

        DB::transaction(function () use ($foodSession, $validated, $user): void {
            $lockedSession = $foodSession->lockForUpdate();
            $lockedSession->addComment($user->id, trim($validated['comment']));

            $this->notifySessionHost(
                $lockedSession,
                $user,
                'food_session_comment',
                $user->name.' mengomentari sesi makan '.$lockedSession->merchant_name.'.'
            );
        });

        return redirect()
            ->route('makan.sessions.comments', $foodSession)
            ->with('success', 'Komentar berhasil dikirim.')
            ->withFragment('komentar');
    }

    private function notifySessionHost(MainModel $foodSession, User $actor, string $type, string $message): void
    {
        if ($foodSession->host_id === $actor->id) {
            return;
        }

        Notification::create([
            'user_id' => $foodSession->host_id,
            'actor_id' => $actor->id,
            'type' => $type,
            'message' => Str::limit($message, 190),
        ]);
    }
}
