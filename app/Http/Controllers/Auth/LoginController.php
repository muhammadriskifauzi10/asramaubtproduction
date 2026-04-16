<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        if (! Auth::check()) {
            return response()->json([
                'success' => false,
                'status' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $logoutLink = config('services.sidara.url').'/sso/logout?redirect_uri='.urlencode(url('/'));

        return redirect()->away($logoutLink);
    }
}
