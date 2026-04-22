<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Providers\RouteServiceProvider;
use App\Http\Middleware\Sso;

class CheckUserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($authenticated = Sso::setLogin()) {

            $user = User::where('sso_id', $authenticated->payload->id)->first();

            if ($user) {
                if ($user->status == 0) {
                    session()->flash('error', 'Anda tidak memiliki akses sistem ini');
                } else {
                    return redirect(RouteServiceProvider::HOME);
                }
            } else {
                if (config('setting.auto_create_user')) {
                    $sso = new Sso();
                    $user = $sso->createUserFromSso($authenticated->payload);
                    auth()->login($user);
                    return redirect(RouteServiceProvider::HOME);
                } else {
                    session()->flash('error', 'Anda tidak memiliki akses sistem ini');
                }
            }
        }
        return $next($request);
    }
}
