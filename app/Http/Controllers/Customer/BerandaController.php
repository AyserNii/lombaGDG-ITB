<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BerandaController extends Controller
{
    public function index()
    {
        $user = Auth::user()->toArray();

        return view('customer.dashboard.index', compact('user'));
    }
}
