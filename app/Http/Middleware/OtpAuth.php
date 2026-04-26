<?php
// app/Http/Middleware/OtpAuth.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class OtpAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::get('user_id')) {
            return redirect()->route('otp.mobile')->with('error', 'Please verify your mobile number first.');
        }

        $user = \App\Models\User::find(Session::get('user_id'));
        if (!$user || $user->is_blocked) {
            Session::flush();
            return redirect()->route('otp.mobile')->with('error', 'Your account has been blocked. Please contact support.');
        }

        return $next($request);
    }
}
