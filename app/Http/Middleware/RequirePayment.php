<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Payment;

class RequirePayment
{
    public function handle(Request $request, Closure $next)
    {
        $user = User::find(Session::get('user_id'));

        if (!$user) {
            return $next($request);
        }

        $hasPaid = Payment::where('user_id', $user->id)
            ->where('status', 'success')
            ->exists();

        if (!$hasPaid) {
            return redirect()->route('payment.unlock')
                ->with('error', 'Please complete the payment to continue.');
        }

        return $next($request);
    }
}
