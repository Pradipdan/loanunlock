<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\LoanApplication;
use App\Services\CreditBureauService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiApplicationController extends Controller
{
    protected CreditBureauService $bureauService;

    public function __construct(CreditBureauService $bureauService)
    {
        $this->bureauService = $bureauService;
    }

    // ── GET /api/application/state ───────────────────────────────────────────
    // Returns the full current state so the app knows which screen to show
    public function getState(Request $request)
    {
        $user = $request->user()->load('latestApplication');
        $app  = $user->latestApplication;

        return response()->json([
            'redirect_to' => $this->determineRedirect($user, $app),
            'user' => [
                'name'               => $user->name,
                'mobile'             => $user->mobile,
                'permissions_granted' => $user->permissions_granted,
            ],
            'application' => $app ? [
                'id'               => $app->id,
                'application_id'   => $app->application_id,
                'status'           => $app->status,
                'requested_amount' => $app->requested_amount,
                'approved_amount'  => $app->approved_amount,
                'tenure_months'    => $app->tenure_months,
                'emi_amount'       => $app->emi_amount,
                'processing_fee'   => $app->processing_fee,
                'credit_score'     => $app->credit_score,
                'is_eligible'      => $app->is_eligible,
            ] : null,
        ]);
    }

    // ── POST /api/application/personal ───────────────────────────────────────
    public function savePersonal(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'               => 'required|string|max:100',
            'pan_number'         => 'required|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/|max:10|unique:users,pan_number,' . $user->id,
            'date_of_birth'      => 'required|date|before:-18 years',
            'state'              => 'required|string',
            'preferred_language' => 'required|string',
            'email'              => 'nullable|email|unique:users,email,' . $user->id,
            'address'            => 'nullable|string|max:255',
            'city'               => 'nullable|string|max:100',
            'pincode'            => 'nullable|digits:6',
        ], [
            'pan_number.regex'      => 'Enter a valid PAN number (e.g. ABCDE1234F).',
            'pan_number.unique'     => 'This PAN is already registered with another account.',
            'email.unique'          => 'This email is already registered with another account.',
            'date_of_birth.before'  => 'You must be at least 18 years old.',
        ]);

        $user->update($validated);

        // Ensure a loan application draft exists
        $app = $user->latestApplication ?? LoanApplication::create([
            'user_id'        => $user->id,
            'application_id' => 'LU' . strtoupper(uniqid()),
            'status'         => 'draft',
        ]);

        if ($app->status === 'draft') {
            $app->update(['status' => 'personal_filled']);
        }

        return response()->json([
            'success'      => true,
            'message'      => 'Personal details saved.',
            'redirect_to'  => 'permissions',
            'application'  => ['id' => $app->id, 'status' => $app->fresh()->status],
        ]);
    }

    // ── POST /api/application/permissions ────────────────────────────────────
    public function savePermissions(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'permissions_granted' => 'required|boolean',
        ]);

        $user->update(['permissions_granted' => $request->permissions_granted]);

        $app = $user->latestApplication;
        if ($app && in_array($app->status, ['draft', 'personal_filled'])) {
            $app->update(['status' => 'permissions_granted']);
        }

        return response()->json([
            'success'     => true,
            'message'     => 'Permissions saved.',
            'redirect_to' => 'loan_details',
        ]);
    }

    // ── POST /api/application/loan-details ───────────────────────────────────
    public function saveLoanDetails(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'requested_amount' => 'required|numeric|min:10000|max:500000',
            'loan_purpose'     => 'required|string|max:100',
            'employment_type'  => 'required|string|in:salaried,self_employed,business,other',
            'monthly_income'   => 'required|numeric|min:5000',
        ]);

        $app = $user->latestApplication;

        if (! $app) {
            return response()->json(['success' => false, 'message' => 'No active application found.'], 422);
        }

        $user->update([
            'employment_type' => $validated['employment_type'],
            'monthly_income'  => $validated['monthly_income'],
        ]);

        $app->update([
            'requested_amount' => $validated['requested_amount'],
            'loan_purpose'     => $validated['loan_purpose'],
            'status'           => 'loan_details_filled',
        ]);

        return response()->json([
            'success'     => true,
            'message'     => 'Loan details saved.',
            'redirect_to' => 'checking_score',
        ]);
    }

    // ── GET /api/application/checking-score ──────────────────────────────────
    // Triggers bureau check and returns result
    public function checkingScore(Request $request)
    {
        $user = $request->user();
        $app  = $user->latestApplication;

        if (! $app) {
            return response()->json(['success' => false, 'message' => 'No application found.'], 422);
        }

        // If bureau check already done, return cached result
        if ($app->credit_score && $app->score_fetched_at) {
            return response()->json([
                'success'      => true,
                'credit_score' => $app->credit_score,
                'is_eligible'  => $app->is_eligible,
                'redirect_to'  => $app->is_eligible ? 'pre_offer' : 'not_eligible',
            ]);
        }

        try {
            $result = $this->bureauService->fetchScore($app);

            $score      = $result['data']['score'] ?? 0;
            $isEligible = $score >= 600;

            $app->update([
                'credit_score'      => $score,
                'is_eligible'       => $isEligible,
                'bureau_name'       => $result['bureau'] ?? 'demo',
                'bureau_raw_response' => json_encode($result),
                'score_fetched_at'  => now(),
                'status'            => $isEligible ? 'eligibility_checked' : 'rejected',
                'rejection_reason'  => $isEligible ? null : 'Credit score below minimum threshold.',
            ]);

            return response()->json([
                'success'      => true,
                'credit_score' => $app->fresh()->credit_score,
                'is_eligible'  => $isEligible,
                'redirect_to'  => $isEligible ? 'pre_offer' : 'not_eligible',
            ]);
        } catch (\Exception $e) {
            Log::error('Bureau check error: ' . $e->getMessage());

            // Demo fallback — always eligible
            $score = rand(650, 800);
            $app->update([
                'credit_score'     => $score,
                'is_eligible'      => true,
                'bureau_name'      => 'demo',
                'score_fetched_at' => now(),
                'status'           => 'eligibility_checked',
            ]);

            return response()->json([
                'success'      => true,
                'credit_score' => $score,
                'is_eligible'  => true,
                'redirect_to'  => 'pre_offer',
            ]);
        }
    }

    // ── GET /api/application/pre-offer ───────────────────────────────────────
    public function preOffer(Request $request)
    {
        $user = $request->user();
        $app  = $user->latestApplication;

        if (! $app) {
            return response()->json(['success' => false, 'message' => 'No application found.'], 422);
        }

        // Accept if explicitly eligible OR if credit score passes threshold
        $eligible = $app->is_eligible || ($app->credit_score && $app->credit_score >= 600);
        if (! $eligible) {
            return response()->json(['success' => false, 'message' => 'No eligible offer found.'], 422);
        }

        // Ensure is_eligible is set correctly
        if (! $app->is_eligible) {
            $app->update(['is_eligible' => true]);
        }

        $processingFee  = (int) config('app.processing_fee', 299);
        $requestedAmount = $app->requested_amount ?? 50000;

        // Calculate offer (simple mock calculation — replace with real logic)
        $approvedAmount = min($requestedAmount, 200000);
        $tenureMonths   = $app->tenure_months ?? 12;
        $interestRate   = 18; // 18% per annum
        $monthlyRate    = $interestRate / 12 / 100;
        $emi            = $monthlyRate > 0
            ? round(($approvedAmount * $monthlyRate * pow(1 + $monthlyRate, $tenureMonths)) / (pow(1 + $monthlyRate, $tenureMonths) - 1))
            : round($approvedAmount / $tenureMonths);

        $app->update([
            'approved_amount' => $approvedAmount,
            'tenure_months'   => $tenureMonths,
            'interest_rate'   => $interestRate,
            'emi_amount'      => $emi,
            'processing_fee'  => $processingFee,
        ]);

        return response()->json([
            'success' => true,
            'offer'   => [
                'approved_amount'  => $approvedAmount,
                'tenure_months'    => $tenureMonths,
                'interest_rate'    => $interestRate,
                'emi_amount'       => $emi,
                'processing_fee'   => $processingFee,
                'credit_score'     => $app->credit_score,
                'application_id'   => $app->application_id,
            ],
        ]);
    }

    // ── GET /api/application/status ──────────────────────────────────────────
    public function status(Request $request)
    {
        $user = $request->user();
        $app  = $user->latestApplication;

        if (! $app) {
            return response()->json(['success' => false, 'message' => 'No application found.'], 404);
        }

        $documents = Document::where('loan_application_id', $app->id)->get()->map(fn($d) => [
            'id'            => $d->id,
            'document_type' => $d->document_type,
            'status'        => $d->status ?? 'uploaded',
            'created_at'    => $d->created_at?->toDateTimeString(),
        ]);

        return response()->json([
            'success' => true,
            'application' => [
                'id'               => $app->id,
                'application_id'   => $app->application_id,
                'status'           => $app->status,
                'requested_amount' => $app->requested_amount,
                'approved_amount'  => $app->approved_amount,
                'tenure_months'    => $app->tenure_months,
                'emi_amount'       => $app->emi_amount,
                'credit_score'     => $app->credit_score,
                'rejection_reason' => $app->rejection_reason,
                'disbursed_at'     => $app->disbursed_at?->toDateTimeString(),
                'created_at'       => $app->created_at?->toDateTimeString(),
            ],
            'documents' => $documents,
        ]);
    }

    // ── POST /api/application/document ───────────────────────────────────────
    public function uploadDocument(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'document_type' => 'required|string|in:aadhaar_front,aadhaar_back,pan_card,salary_slip,bank_statement,photo',
            'file'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $app = $user->latestApplication;

        if (! $app) {
            return response()->json(['success' => false, 'message' => 'No active application.'], 422);
        }

        $path = $request->file('file')->store(
            'documents/' . $user->id,
            'local'
        );

        $document = Document::create([
            'user_id'             => $user->id,
            'loan_application_id' => $app->id,
            'document_type'       => $request->document_type,
            'file_path'           => $path,
            'status'              => 'uploaded',
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Document uploaded successfully.',
            'document' => [
                'id'            => $document->id,
                'document_type' => $document->document_type,
                'status'        => $document->status,
            ],
        ]);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────
    private function determineRedirect($user, $app): string
    {
        if (! $user->name) return 'personal_details';
        if (! $user->permissions_granted) return 'permissions';
        if (! $app) return 'dashboard';

        return match (true) {
            in_array($app->status, ['payment_done', 'under_review', 'approved', 'disbursed', 'rejected']) => 'dashboard',
            $app->status === 'payment_pending'                                                             => 'unlock',
            in_array($app->status, ['eligibility_checked', 'loan_details_filled'])                        => 'pre_offer',
            $app->status === 'permissions_granted'                                                         => 'loan_details',
            $app->status === 'personal_filled'                                                             => 'permissions',
            default                                                                                        => 'dashboard',
        };
    }
}
