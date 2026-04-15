<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Historislogin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'status'  => 401,
                'message' => 'Unauthorized',
            ], 401);
        }

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        if (Auth::check()) {

            Auth::logout();
            // request()->session()->invalidate();
            // request()->session()->regenerateToken();
        }

        $logoutLink = 'https://sidara.ubtsu.ac.id/sso/logout?redirect_uri=' . urlencode(url('/'));
        // return redirect()->away($logoutLink);
    }
}
