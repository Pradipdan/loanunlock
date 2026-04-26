<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\LoanApplication;
use App\Models\Document;
use App\Services\CreditBureauService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ApplicationController extends Controller
{
    protected $bureauService;

    public function __construct(CreditBureauService $bureauService)
    {
        $this->bureauService = $bureauService;
    }

    private function currentUser()
    {
        return \App\Models\User::find(Session::get('user_id'));
    }

    // ─── Splash ─────────────────────────────────────────────────────────────
    public function splash()
    {
        return view('user.splash');
    }

    // ─── Personal Details ────────────────────────────────────────────────────
    public function personalDetails()
    {
        $user = $this->currentUser();
        $application = $user->latestApplication ?? LoanApplication::create([
            'user_id'        => $user->id,
            'application_id' => 'LU' . strtoupper(uniqid()),
            'status'         => 'draft',
        ]);
        return view('user.personal', compact('user', 'application'));
    }

    public function savePersonal(Request $request)
    {
        $user = $this->currentUser();

        $request->validate([
            'name'               => 'required|string|max:100',
            'pan_number'         => 'required|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/|max:10|unique:users,pan_number,' . $user->id,
            'date_of_birth'      => 'required|date|before:-18 years',
            'state'              => 'required|string',
            'preferred_language' => 'required|string',
            'email'              => 'nullable|email|unique:users,email,' . $user->id,
            'address'            => 'nullable|string|max:255',
            'city'               => 'nullable|string|max:100',
            'pincode'            => 'nullable|digits:6',
            'consent'            => 'required|accepted',
        ], [
            'consent.required'         => 'Please agree to the terms and authorize the credit check.',
            'pan_number.regex'         => 'Enter a valid PAN number (e.g. ABCDE1234F).',
            'pan_number.unique'        => 'This PAN is already registered with another account.',
            'email.unique'             => 'This email is already registered with another account.',
            'date_of_birth.before'     => 'You must be at least 18 years old.',
        ]);

        $user->update($request->only([
            'name',
            'pan_number',
            'date_of_birth',
            'state',
            'preferred_language',
            'email',
            'address',
            'city',
            'pincode',
        ]));

        // Update application status
        $app = $user->latestApplication;
        if ($app && $app->status === 'draft') {
            $app->update(['status' => 'personal_filled']);
        }

        return redirect()->route('application.permissions');
    }

    // ─── Permissions ─────────────────────────────────────────────────────────
    public function permissions()
    {
        return view('user.permissions');
    }

    public function savePermissions(Request $request)
    {
        $user = $this->currentUser();
        $user->update(['permissions_granted' => true]);
        return redirect()->route('application.loan');
    }

    // ─── Loan Details ────────────────────────────────────────────────────────
    public function loanDetails()
    {
        $user = $this->currentUser();
        $application = $user->latestApplication;
        return view('user.loan_details', compact('user', 'application'));
    }

    public function saveLoanDetails(Request $request)
    {
        $request->validate([
            'employment_type' => 'required|in:salaried,business',
            'monthly_income'  => 'required|numeric|min:5000',
            'company_name'    => 'required|string|max:100',
            'loan_purpose'    => 'required|string|max:100',
            'requested_amount' => 'required|numeric|min:5000|max:500000',
            'tenure_months'   => 'required|in:3,6,12,18,24,36',
        ]);

        $user = $this->currentUser();
        $user->update([
            'employment_type' => $request->employment_type,
            'monthly_income'  => $request->monthly_income,
            'company_name'    => $request->company_name,
        ]);

        $app = $user->latestApplication;
        if ($app) {
            $app->update([
                'employment_type'  => $request->employment_type,
                'loan_purpose'     => $request->loan_purpose,
                'requested_amount' => $request->requested_amount,
                'tenure_months'    => $request->tenure_months,
            ]);
        }

        // ── Instant eligibility engine ─────────────────────────────────────
        // Compute a pre-approved offer (always slightly higher than requested)
        // to create a compelling payment-wall moment.
        $income       = (float) $request->monthly_income;
        $requested    = (float) $request->requested_amount;
        $tenure       = (int)   $request->tenure_months;
        $interestRate = 18.0;
        $creditScore  = rand(720, 790); // always good range for marketing

        // Pre-approved = requested × multiplier (1.4–1.8), capped at income × 10
        $multiplier     = round(mt_rand(140, 180) / 100, 2); // e.g. 1.62
        $preApproved    = min(ceil(($requested * $multiplier) / 1000) * 1000, $income * 10, 500000);
        $approvedAmount = $preApproved;

        // EMI on pre-approved amount
        $monthlyRate = ($interestRate / 100) / 12;
        $emi = round(
            ($approvedAmount * $monthlyRate * pow(1 + $monthlyRate, $tenure)) /
                (pow(1 + $monthlyRate, $tenure) - 1),
            2
        );

        if ($app) {
            $app->update([
                'is_eligible'     => true,
                'approved_amount' => $approvedAmount,
                'interest_rate'   => $interestRate,
                'emi_amount'      => $emi,
                // 'credit_score' => $creditScore, // Removed: handled by bureau service now
                'status'          => 'eligibility_checked',
            ]);
        }

        return redirect()->route('application.checking');
    }

    // ─── Checking Score (Bureau Integration) ─────────────────────────────────
    public function checkingScore()
    {
        $user = $this->currentUser();
        $application = $user->latestApplication;

        if (!$application) {
            return redirect()->route('application.loan');
        }

        // Fetch the real/mock score from the bureau service
        $this->bureauService->fetchScore($application);

        return view('user.checking_score');
    }

    // ─── Pre-Approved Offer (Payment Wall) ───────────────────────────────────
    public function preApprovedOffer()
    {
        $user        = $this->currentUser();
        $application = $user->latestApplication;

        // Guard: must have gone through loan details
        if (!$application || !$application->requested_amount) {
            return redirect()->route('application.loan');
        }

        // Already fully paid → go to status
        if (in_array($application->status, ['payment_done', 'under_review', 'approved', 'disbursed'])) {
            return redirect()->route('application.status');
        }

        return view('user.pre_offer', compact('user', 'application'));
    }

    // ─── Eligibility (legacy – kept for back-compat) ─────────────────────────
    public function eligibility()
    {
        $user        = $this->currentUser();
        $application = $user->latestApplication;

        if (!$application || !$application->requested_amount) {
            return redirect()->route('application.loan');
        }

        // Redirect to the new pre-offer payment wall instead
        return redirect()->route('application.pre_offer');
    }

    public function checkEligibility(Request $request)
    {
        // Legacy endpoint – just send them to the pre-offer wall
        return redirect()->route('application.pre_offer');
    }

    // ─── Document Upload ─────────────────────────────────────────────────────
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'document_type' => 'required|in:pan,aadhar_front,aadhar_back,selfie,salary_slip,bank_statement,itr,business_proof',
            'file'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $user = $this->currentUser();
        $path = $request->file('file')->store('documents/' . $user->id, 'public');

        $doc = Document::updateOrCreate(
            ['user_id' => $user->id, 'type' => $request->document_type],
            [
                'loan_application_id' => $user->latestApplication?->id,
                'file_path'           => $path,
                'original_name'       => $request->file('file')->getClientOriginalName(),
                'verification_status' => 'pending',
            ]
        );

        return response()->json(['success' => true, 'message' => 'Document uploaded.', 'doc_id' => $doc->id]);
    }
}
