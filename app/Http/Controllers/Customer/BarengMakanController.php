<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\BarengMakanModel as MainModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
        $this->routeName = '/' . $this->role . '/' . $this->module . '/' . $this->page;
        $this->basePath = $this->role . '.' . $this->module . '.';
        $this->routePrefix = $this->role . '.' . $this->page . '.';
        $this->alertKey = 'notifikasi' . $this->role . $this->module;
        $this->lastPageKey = 'url' . $this->role . $this->module . $this->page;
        $this->sessionNamespace = 'sesi' . $this->role . $this->module;
    }

    public function list(Request $request, $halaman = '')
    {
        if (!empty($halaman) && !filter_var($halaman, FILTER_VALIDATE_INT)) {
            return redirect()->route($this->routePrefix . 'list');
        } else if (!empty($halaman) && ($halaman == 1 || $halaman < 1)) {
            return redirect()->route($this->routePrefix . 'list');
        } else if (empty($halaman)) {
            $halaman = 1;
        }

        $limit = 10;
        $offset = ($halaman - 1) * $limit;
        $makshalaman = 100;
        $maksoffset = $limit * $makshalaman;
        if ($offset > $maksoffset) {
            $offset = $maksoffset;
        }

        $totalsesimakan = MainModel::total_sesimakan($maksoffset);
        if ($totalsesimakan && $totalsesimakan->total > 0) {
            $listsesimakan = MainModel::list_sesimakan($limit, $offset);
        } else {
            $listsesimakan = [];
        }

        $hasilsesimakan = new LengthAwarePaginator(
            $listsesimakan,
            $totalsesimakan->total ?? 0,
            $limit,
            $halaman
        );

        $hasilsesimakan->withPath($this->routeName);

        $user = Auth::user() ? Auth::user()->toArray() : session('user', [
            'name' => 'Grace Stanley',
            'location' => 'Beji, Depok',
        ]);

        $datakonten['judulcore'] = $this->title;
        $datakonten['caricore'] = $this->routePrefix . "caridata";
        $datakonten['caricoredata'] = "";
        $datakonten['parameterdata'] = "";
        $datakonten['activeSessions'] = $listsesimakan;
        $datakonten['datasesimakan'] = $hasilsesimakan;
        $datakonten['user'] = $user;
        $datakonten['basePath'] = $this->basePath;
        $datakonten['routePrefix'] = $this->routePrefix;
        $datakonten['sessionNamespace'] = $this->sessionNamespace;

        return view('customer.makan.index', $datakonten);
    }

    public function index()
    {
        return $this->list(request());
    }

    public function caridata(Request $request): RedirectResponse
    {
        $cari = $request->input('cari', '');
        $carirubah = str_replace('/', '+', $cari);
        $carirubah = preg_split('/\s+/', $carirubah);
        $carirubah = implode('-', $carirubah);

        if (empty($cari)) {
            $pesan = "Kolom cari tidak boleh kosong!";
            return redirect()->back()->with([$this->alertKey => 'error', 'pesan' => $pesan]);
        }

        return redirect()->route($this->routePrefix . 'cari', ['cari' => $carirubah])->withInput();
    }

    public function cari(Request $request, $cari = '', $halaman = '')
    {
        $cari = str_replace('+', '/', $cari);
        $cari = str_replace('-', ' ', $cari);
        $carirubah = str_replace('/', '+', $cari);
        $carirubah = preg_split('/\s+/', $carirubah);
        $carirubah = implode('-', $carirubah);

        if (!empty($halaman) && !filter_var($halaman, FILTER_VALIDATE_INT)) {
            return redirect()->route($this->routePrefix . 'cari', ['cari' => $carirubah]);
        } else if (!empty($halaman) && ($halaman == 1 || $halaman < 1)) {
            return redirect()->route($this->routePrefix . 'cari', ['cari' => $carirubah]);
        } else if (empty($halaman)) {
            $halaman = 1;
        }

        $limit = 10;
        $offset = ($halaman - 1) * $limit;
        $makshalaman = 100;
        $maksoffset = $limit * $makshalaman;
        if ($offset > $maksoffset) {
            $offset = $maksoffset;
        }

        $totalsesimakan = MainModel::total_sesimakancari($cari, $maksoffset);
        if ($totalsesimakan && $totalsesimakan->total > 0) {
            $listsesimakan = MainModel::list_sesimakancari($cari, $limit, $offset);
        } else {
            $listsesimakan = [];
        }

        $hasilsesimakan = new LengthAwarePaginator(
            $listsesimakan,
            $totalsesimakan->total ?? 0,
            $limit,
            $halaman
        );

        $hasilsesimakan->withPath($this->routeName . '/cari/' . $carirubah);

        $user = Auth::user() ? Auth::user()->toArray() : session('user', [
            'name' => 'Grace Stanley',
            'location' => 'Beji, Depok',
        ]);

        $datakonten['judulcore'] = $this->title;
        $datakonten['menucore'] = $this->routePrefix . "list";
        $datakonten['caricore'] = $this->routePrefix . "caridata";
        $datakonten['caricoredata'] = $cari;
        $datakonten['parameterdata'] = "";
        $datakonten['activeSessions'] = $listsesimakan;
        $datakonten['datasesimakan'] = $hasilsesimakan;
        $datakonten['user'] = $user;
        $datakonten['basePath'] = $this->basePath;
        $datakonten['routePrefix'] = $this->routePrefix;
        $datakonten['sessionNamespace'] = $this->sessionNamespace;

        return view('customer.makan.index', $datakonten);
    }
}
