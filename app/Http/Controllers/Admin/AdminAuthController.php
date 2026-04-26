<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        if (Session::get('admin_id')) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        $admin = Admin::where('email', $request->email)->where('is_active', true)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return back()->with('error', 'Invalid credentials. Please try again.')->withInput();
        }

        $admin->update(['last_login_at' => now()]);
        Session::put('admin_id', $admin->id);
        Session::put('admin_name', $admin->name);
        Session::put('admin_role', $admin->role);

        return redirect()->route('admin.dashboard');
    }

    public function logout()
    {
        Session::forget(['admin_id', 'admin_name', 'admin_role']);
        return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
    }
}
