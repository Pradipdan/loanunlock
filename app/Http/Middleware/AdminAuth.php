<?php
// app/Http/Middleware/AdminAuth.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::get('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Please login to access admin panel.');
        }

        $admin = \App\Models\Admin::find(Session::get('admin_id'));
        if (!$admin || !$admin->is_active) {
            Session::forget(['admin_id', 'admin_name', 'admin_role']);
            return redirect()->route('admin.login')->with('error', 'Admin account is inactive.');
        }

        return $next($request);
    }
}
