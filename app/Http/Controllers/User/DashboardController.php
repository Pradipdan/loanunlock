<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    private function currentUser()
    {
        return \App\Models\User::find(Session::get('user_id'));
    }

    public function index()
    {
        $user = $this->currentUser();
        $applications = $user->loanApplications()->orderByDesc('created_at')->get();
        $latestApp    = $user->latestApplication;
        return view('user.dashboard', compact('user', 'applications', 'latestApp'));
    }

    public function status()
    {
        $user        = $this->currentUser();
        $application = $user->latestApplication;

        if (!$application) {
            return redirect()->route('application.personal');
        }

        return view('user.status', compact('user', 'application'));
    }

    public function logout()
    {
        Session::flush();
        return redirect()->route('splash')->with('success', 'Logged out successfully.');
    }
}
