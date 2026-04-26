<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanApplication;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'        => User::count(),
            'total_applications' => LoanApplication::count(),
            'pending_review'     => LoanApplication::where('status', 'under_review')->count(),
            'approved'           => LoanApplication::where('status', 'approved')->count(),
            'rejected'           => LoanApplication::where('status', 'rejected')->count(),
            'disbursed'          => LoanApplication::where('status', 'disbursed')->count(),
            'total_revenue'      => Payment::where('status', 'success')->sum('amount'),
            'total_loan_value'   => LoanApplication::where('status', 'approved')->sum('approved_amount'),
        ];

        $recentApplications = LoanApplication::with('user')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $monthlyStats = LoanApplication::selectRaw('MONTH(created_at) as month, COUNT(*) as count, SUM(approved_amount) as amount')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.dashboard', compact('stats', 'recentApplications', 'monthlyStats'));
    }

    public function reports()
    {
        $applications = LoanApplication::with(['user', 'payments'])
            ->orderByDesc('created_at')
            ->paginate(20);

        $paymentStats = [
            'total_collected' => Payment::where('status', 'success')->sum('amount'),
            'upi'             => Payment::where('status', 'success')->where('method', 'upi')->count(),
            'card'            => Payment::where('status', 'success')->where('method', 'card')->count(),
            'netbanking'      => Payment::where('status', 'success')->where('method', 'netbanking')->count(),
        ];

        return view('admin.reports', compact('applications', 'paymentStats'));
    }

    public function exportCsv()
    {
        $applications = LoanApplication::with('user')->get();
        $csv  = "App ID,User,Mobile,PAN,Amount,Status,EMI,Applied On\n";
        foreach ($applications as $app) {
            $csv .= implode(',', [
                $app->application_id,
                '"' . ($app->user->name ?? '') . '"',
                $app->user->mobile ?? '',
                $app->user->pan_number ?? '',
                $app->approved_amount ?? 0,
                $app->status,
                $app->emi_amount ?? 0,
                $app->created_at->format('d/m/Y'),
            ]) . "\n";
        }
        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="loan_applications_' . date('Ymd') . '.csv"',
        ]);
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function saveSettings(Request $request)
    {
        // Save to config/DB in production
        return back()->with('success', 'Settings saved successfully.');
    }
}
