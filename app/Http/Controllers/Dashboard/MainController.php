<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Dasbor',
        ];

        return view('contents.dashboard.main', $data);
    }
    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $logoutLink = config('services.sidara.url') . '/sso/logout?redirect_uri=' . urlencode(url('/'));

        return redirect()->away($logoutLink);
    }
}
