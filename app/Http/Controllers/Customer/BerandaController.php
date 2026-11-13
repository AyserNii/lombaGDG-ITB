<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\BerandaModel as MainModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

header('Cache-Control: no-store, private, no-cache, must-revalidate');
header('Cache-Control: pre-check=0, post-check=0, max-age=0, max-stale = 0', false);
header('Pragma: public');
header('Pragma: no-cache');

class BerandaController extends Controller
{
    private $role = 'customer';
    private $module = 'dashboard';
    private $page = 'beranda';
    private $title = 'Dashboard BARENG';

    private $sessionNamespace;
    private $basePath;
    private $routePrefix;
    private $routeName;
    private $lastPageKey;
    private $alertKey;

    public function __construct()
    {
        $this->routeName = '/' . $this->role . '/' . $this->page;
        $this->basePath = $this->role . '.' . $this->module . '.';
        $this->routePrefix = $this->role . '.' . $this->page . '.';
        $this->alertKey = 'notifikasi' . $this->role . $this->module;
        $this->lastPageKey = 'url' . $this->role . $this->module . $this->page;
        $this->sessionNamespace = 'sesi' . $this->role . $this->module;
    }

    public function list(Request $request, $halaman = '')
    {
        return $this->index();
    }

    public function index() 
    {
        $authUser = Auth::user();
        $user = MainModel::cek_login($authUser->id);

        $ringkasan = $authUser ? MainModel::data_ringkasan($authUser->id) : null;
        $communityPosts = MainModel::community_posts();

        $datakonten['judulcore'] = $this->title;
        $datakonten['user'] = $user;
        $datakonten['ringkasan'] = $ringkasan;
        $datakonten['communityPosts'] = $communityPosts;
        $datakonten['basePath'] = $this->basePath;
        $datakonten['routePrefix'] = $this->routePrefix;
        $datakonten['sessionNamespace'] = $this->sessionNamespace;

        return view('customer.dashboard.index', $datakonten);
    }
}
