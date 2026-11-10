<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\SesiAktifModel as MainModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

header('Cache-Control: no-store, private, no-cache, must-revalidate');
header('Cache-Control: pre-check=0, post-check=0, max-age=0, max-stale = 0', false);
header('Pragma: public');
header('Pragma: no-cache');

class SesiAktifController extends Controller
{
    private $role = 'customer';
    private $module = 'gotongroyong';
    private $page = 'sesi';
    private $title = 'Sesi Aktif';

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

        $totalsesi = MainModel::total_sesiaktif($maksoffset);
        if ($totalsesi && $totalsesi->total > 0) {
            $listsesi = MainModel::list_sesiaktif($limit, $offset);
        } else {
            $listsesi = [];
        }

        $hasilsesi = new LengthAwarePaginator(
            $listsesi,
            $totalsesi->total ?? 0,
            $limit,
            $halaman
        );

        $hasilsesi->withPath($this->routeName);

        $user = Auth::user() ? Auth::user()->toArray() : session('user', [
            'name' => 'Grace Stanley',
            'location' => 'Beji, Depok',
        ]);

        $exploreSessions = MainModel::list_exploresesi(10, 0);

        $datakonten['judulcore'] = $this->title;
        $datakonten['caricore'] = $this->routePrefix . "caridata";
        $datakonten['caricoredata'] = "";
        $datakonten['parameterdata'] = "";
        $datakonten['mySessions'] = $listsesi;
        $datakonten['exploreSessions'] = $exploreSessions;
        $datakonten['datasesi'] = $hasilsesi;
        $datakonten['user'] = $user;
        $datakonten['basePath'] = $this->basePath;
        $datakonten['routePrefix'] = $this->routePrefix;
        $datakonten['sessionNamespace'] = $this->sessionNamespace;

        return view('customer.sesi.index', $datakonten);
    }

    public function index()
    {
        return $this->list(request());
    }
}
