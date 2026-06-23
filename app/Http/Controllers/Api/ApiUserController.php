<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiUserController extends Controller
{
    // ── GET /api/user/profile ────────────────────────────────────────────────
    public function profile(Request $request)
    {
        $user = $request->user()->load('latestApplication');
        $app  = $user->latestApplication;

        return response()->json([
            'user' => [
                'id'                 => $user->id,
                'name'               => $user->name,
                'mobile'             => $user->mobile,
                'email'              => $user->email,
                'pan_number'         => $user->pan_number,
                'date_of_birth'      => $user->date_of_birth?->toDateString(),
                'state'              => $user->state,
                'preferred_language' => $user->preferred_language,
                'employment_type'    => $user->employment_type,
                'monthly_income'     => $user->monthly_income,
                'city'               => $user->city,
                'address'            => $user->address,
                'pincode'            => $user->pincode,
                'is_blocked'         => $user->is_blocked,
                'permissions_granted' => $user->permissions_granted,
            ],
            'application' => $app ? [
                'id'               => $app->id,
                'application_id'   => $app->application_id,
                'status'           => $app->status,
                'requested_amount' => $app->requested_amount,
                'approved_amount'  => $app->approved_amount,
                'tenure_months'    => $app->tenure_months,
                'interest_rate'    => $app->interest_rate,
                'emi_amount'       => $app->emi_amount,
                'processing_fee'   => $app->processing_fee,
                'credit_score'     => $app->credit_score,
                'is_eligible'      => $app->is_eligible,
                'rejection_reason' => $app->rejection_reason,
                'created_at'       => $app->created_at?->toDateTimeString(),
            ] : null,
        ]);
    }

    // ── POST /api/user/fcm-token ─────────────────────────────────────────────
    public function storeFcmToken(Request $request)
    {
        $request->validate(['fcm_token' => 'required|string']);
        $request->user()->update(['fcm_token' => $request->fcm_token]);

        return response()->json(['success' => true]);
    }

    // ── POST /api/auth/logout ────────────────────────────────────────────────
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['success' => true, 'message' => 'Logged out successfully.']);
    }
}
