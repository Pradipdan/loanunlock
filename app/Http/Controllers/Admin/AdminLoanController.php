<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanApplication;
use App\Models\LoanNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminLoanController extends Controller
{
    public function index(Request $request)
    {
        $query = LoanApplication::with('user')->orderByDesc('created_at');

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $q = $request->search;
            $query->where(function ($q2) use ($q) {
                $q2->where('application_id', 'like', "%$q%")
                   ->orWhereHas('user', fn($u) => $u->where('mobile', 'like', "%$q%")
                       ->orWhere('name', 'like', "%$q%")
                       ->orWhere('pan_number', 'like', "%$q%"));
            });
        }

        $applications = $query->paginate(15)->withQueryString();
        $statusCounts = LoanApplication::selectRaw('status, count(*) as count')
            ->groupBy('status')->pluck('count', 'status');

        return view('admin.loans.index', compact('applications', 'statusCounts'));
    }

    public function show($id)
    {
        $application = LoanApplication::with([
            'user', 'payments', 'documents', 'notes.admin', 'reviewer',
        ])->findOrFail($id);

        return view('admin.loans.show', compact('application'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'approved_amount' => 'required|numeric|min:1000',
            'tenure_months'   => 'required|in:3,6,12,18,24,36',
            'interest_rate'   => 'required|numeric|min:1|max:36',
        ]);

        $application = LoanApplication::findOrFail($id);

        $monthlyRate  = ($request->interest_rate / 100) / 12;
        $n            = $request->tenure_months;
        $p            = $request->approved_amount;
        $emi          = round(($p * $monthlyRate * pow(1 + $monthlyRate, $n)) / (pow(1 + $monthlyRate, $n) - 1), 2);

        $application->update([
            'status'          => 'approved',
            'approved_amount' => $request->approved_amount,
            'tenure_months'   => $request->tenure_months,
            'interest_rate'   => $request->interest_rate,
            'emi_amount'      => $emi,
            'reviewed_by'     => Session::get('admin_id'),
            'reviewed_at'     => now(),
        ]);

        LoanNote::create([
            'loan_application_id' => $application->id,
            'admin_id'            => Session::get('admin_id'),
            'note'                => $request->note ?? 'Loan approved.',
            'type'                => 'approval',
        ]);

        return back()->with('success', 'Loan approved successfully!');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
        ]);

        $application = LoanApplication::findOrFail($id);
        $application->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by'      => Session::get('admin_id'),
            'reviewed_at'      => now(),
        ]);

        LoanNote::create([
            'loan_application_id' => $application->id,
            'admin_id'            => Session::get('admin_id'),
            'note'                => 'Rejected: ' . $request->rejection_reason,
            'type'                => 'rejection',
        ]);

        return back()->with('success', 'Loan rejected.');
    }

    public function disburse(Request $request, $id)
    {
        $application = LoanApplication::findOrFail($id);
        if ($application->status !== 'approved') {
            return back()->with('error', 'Only approved loans can be disbursed.');
        }

        $application->update([
            'status'       => 'disbursed',
            'disbursed_at' => now(),
        ]);

        LoanNote::create([
            'loan_application_id' => $application->id,
            'admin_id'            => Session::get('admin_id'),
            'note'                => $request->note ?? 'Loan disbursed to borrower.',
            'type'                => 'disbursement',
        ]);

        return back()->with('success', 'Loan marked as disbursed!');
    }

    public function documents($id)
    {
        $application = LoanApplication::with(['user', 'documents'])->findOrFail($id);
        return view('admin.loans.documents', compact('application'));
    }

    public function addNote(Request $request, $id)
    {
        $request->validate(['note' => 'required|string|min:3']);
        $application = LoanApplication::findOrFail($id);

        LoanNote::create([
            'loan_application_id' => $application->id,
            'admin_id'            => Session::get('admin_id'),
            'note'                => $request->note,
            'type'                => $request->type ?? 'general',
        ]);

        return back()->with('success', 'Note added.');
    }
}
