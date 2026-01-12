<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return view('dashboards.admin');
        }

        if ($user->hasRole('kasir')) {
            return view('dashboards.kasir');
        }

        if ($user->hasRole('owner')) {
            return view('dashboards.owner');
        }

        // Default or fallthrough
        return view('dashboards.admin');
    }
}
