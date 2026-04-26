<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('loanApplications')->orderByDesc('created_at');

        if ($request->search) {
            $q = $request->search;
            $query->where(function ($q2) use ($q) {
                $q2->where('mobile', 'like', "%$q%")
                   ->orWhere('name', 'like', "%$q%")
                   ->orWhere('pan_number', 'like', "%$q%")
                   ->orWhere('email', 'like', "%$q%");
            });
        }
        if ($request->status === 'blocked') {
            $query->where('is_blocked', true);
        } elseif ($request->status === 'verified') {
            $query->where('is_verified', true);
        }

        $users = $query->paginate(15)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::with(['loanApplications.payments', 'documents'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function block(Request $request, $id)
    {
        User::findOrFail($id)->update(['is_blocked' => true]);
        return back()->with('success', 'User blocked.');
    }

    public function unblock(Request $request, $id)
    {
        User::findOrFail($id)->update(['is_blocked' => false]);
        return back()->with('success', 'User unblocked.');
    }
}
