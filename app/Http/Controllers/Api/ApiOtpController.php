<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiOtpController extends Controller
{
    // ── Generate 6-digit OTP ────────────────────────────────────────────────
    private function generateOtp(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    // ── Send OTP via SMS provider ───────────────────────────────────────────
    private function sendOtpViaSms(string $mobile): array
    {
        $provider = strtolower(env('OTP_PROVIDER', 'demo'));

        return match ($provider) {
            'fast2sms' => $this->sendViaFast2Sms($mobile),
            '2factor'  => $this->sendVia2Factor($mobile),
            default    => $this->demoOtp(),
        };
    }

    private function sendViaFast2Sms(string $mobile): array
    {
        $apiKey = env('FAST2SMS_API_KEY');

        if (empty($apiKey) || $apiKey === 'your_fast2sms_api_key_here') {
            return $this->demoOtp();
        }

        $otp    = $this->generateOtp();
        $routes = [
            ['route' => 'otp', 'variables_values' => $otp, 'flash' => 0, 'numbers' => $mobile],
            ['route' => 'q',   'message' => "Your LoanUnlock OTP is {$otp}", 'flash' => 0, 'numbers' => $mobile],
        ];

        foreach ($routes as $params) {
            try {
                $url      = 'https://www.fast2sms.com/dev/bulkV2?' . http_build_query(
                    array_merge(['authorization' => $apiKey], $params)
                );
                $response = Http::timeout(10)->get($url);
                $body     = $response->json();

                if (($body['return'] ?? false) === true) {
                    return ['success' => true, 'session_id' => 'DB_VERIFY', 'otp_code' => $otp, 'demo' => false];
                }

                if (in_array($body['status_code'] ?? 0, [996, 999])) {
                    continue;
                }

                $msg = is_array($body['message'] ?? null)
                    ? implode(', ', $body['message'])
                    : ($body['message'] ?? 'Failed to send OTP.');

                return ['success' => false, 'message' => $msg];
            } catch (\Exception $e) {
                Log::error('Fast2SMS API error: ' . $e->getMessage());
            }
        }

        return ['success' => false, 'message' => 'SMS service unavailable. Please try again.'];
    }

    private function sendVia2Factor(string $mobile): array
    {
        $apiKey = env('TWOFACTOR_API_KEY');

        if (empty($apiKey) || $apiKey === 'your_2factor_api_key_here') {
            return $this->demoOtp();
        }

        $otp = $this->generateOtp();

        try {
            $template = rawurlencode(env('TWOFACTOR_TEMPLATE', 'AUTOGEN'));
            $response = Http::timeout(10)->get(
                "https://2factor.in/API/V1/{$apiKey}/SMS/+91{$mobile}/{$otp}/{$template}"
            );
            $body = $response->json();

            if (($body['Status'] ?? '') === 'Success') {
                return ['success' => true, 'session_id' => 'DB_VERIFY', 'otp_code' => $otp, 'demo' => false];
            }

            return ['success' => false, 'message' => 'Failed to send OTP. Please try again.'];
        } catch (\Exception $e) {
            Log::error('2Factor SMS error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'SMS service unavailable.'];
        }
    }

    private function demoOtp(): array
    {
        return ['success' => true, 'session_id' => 'DEMO_SESSION', 'otp_code' => '123456', 'demo' => true];
    }

    private function verifyOtpViaSms(string $sessionId, string $otpCode, ?string $storedOtp = null): bool
    {
        $provider = strtolower(env('OTP_PROVIDER', 'demo'));

        if ($provider === 'fast2sms' || in_array($sessionId, ['DEMO_SESSION', 'DB_VERIFY'])) {
            return $storedOtp !== null && $storedOtp === $otpCode;
        }

        $apiKey = env('TWOFACTOR_API_KEY');

        try {
            $response = Http::timeout(10)->get(
                "https://2factor.in/API/V1/{$apiKey}/SMS/VERIFY/{$sessionId}/{$otpCode}"
            );
            $body = $response->json();
            return ($body['Status'] ?? '') === 'Success' && ($body['Details'] ?? '') === 'OTP Matched';
        } catch (\Exception $e) {
            return false;
        }
    }

    // ── POST /api/otp/send ───────────────────────────────────────────────────
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile'  => 'required|digits:10',
        ], [
            'mobile.required' => 'Mobile number is required.',
            'mobile.digits'   => 'Enter a valid 10-digit mobile number.',
        ]);

        $mobile = $request->mobile;

        // Rate limit: max 3 OTPs per 15 minutes
        $recentCount = Otp::where('mobile', $mobile)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->count();

        if ($recentCount >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Too many OTP requests. Please try after 15 minutes.',
            ], 429);
        }

        // Invalidate old OTPs
        Otp::where('mobile', $mobile)->where('is_used', false)->update(['is_used' => true]);

        $result = $this->sendOtpViaSms($mobile);

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to send OTP.',
            ], 500);
        }

        // Store in DB
        Otp::create([
            'mobile'     => $mobile,
            'otp'        => $result['otp_code'] ?? '------',
            'is_used'    => false,
            'attempts'   => 0,
            'expires_at' => now()->addMinutes(10),
        ]);

        $masked  = '+91 ' . substr($mobile, 0, 2) . 'xxxxxx' . substr($mobile, -2);
        $message = ($result['demo'] ?? false)
            ? 'Demo mode — use OTP: 123456'
            : 'OTP sent to ' . $masked . '. Valid for 10 minutes.';

        return response()->json([
            'success'    => true,
            'demo'       => $result['demo'] ?? false,
            'message'    => $message,
            'session_id' => $result['session_id'],
        ]);
    }

    // ── POST /api/otp/verify ─────────────────────────────────────────────────
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile'     => 'required|digits:10',
            'otp'        => 'required|digits:6',
            'session_id' => 'required|string',
        ]);

        $mobile    = $request->mobile;
        $sessionId = $request->session_id;

        $otpRecord = Otp::where('mobile', $mobile)
            ->where('is_used', false)
            ->orderByDesc('created_at')
            ->first();

        if (! $otpRecord) {
            return response()->json(['success' => false, 'message' => 'OTP expired. Please request a new one.'], 422);
        }

        if ($otpRecord->expires_at->isPast()) {
            $otpRecord->update(['is_used' => true]);
            return response()->json(['success' => false, 'message' => 'OTP expired. Please request a new OTP.'], 422);
        }

        $otpRecord->increment('attempts');

        if ($otpRecord->attempts > 5) {
            $otpRecord->update(['is_used' => true]);
            return response()->json(['success' => false, 'message' => 'Too many attempts. Please request a new OTP.'], 429);
        }

        $isValid = $this->verifyOtpViaSms($sessionId, $request->otp, $otpRecord->otp);

        if (! $isValid) {
            $remaining = 5 - $otpRecord->attempts;
            return response()->json([
                'success'  => false,
                'message'  => "Incorrect OTP. {$remaining} attempts remaining.",
            ], 422);
        }

        // ✅ Verified
        $otpRecord->update(['is_used' => true]);

        $user = User::firstOrCreate(
            ['mobile' => $mobile],
            ['is_verified' => true]
        );
        $user->update(['is_verified' => true]);

        // Revoke old mobile tokens and issue fresh one
        $user->tokens()->where('name', 'mobile-app')->delete();
        $token = $user->createToken('mobile-app')->plainTextToken;

        // Determine where the user should go in the app flow
        $redirectTo = $this->getRedirectTarget($user);

        return response()->json([
            'success'     => true,
            'token'       => $token,
            'user'        => [
                'id'     => $user->id,
                'mobile' => $user->mobile,
                'name'   => $user->name,
                'email'  => $user->email,
            ],
            'redirect_to' => $redirectTo,
        ]);
    }

    // ── POST /api/otp/resend ─────────────────────────────────────────────────
    public function resendOtp(Request $request)
    {
        $user   = $request->user();
        $mobile = $user->mobile;

        $recentCount = Otp::where('mobile', $mobile)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->count();

        if ($recentCount >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Too many OTP requests. Try after 15 minutes.',
            ], 429);
        }

        Otp::where('mobile', $mobile)->where('is_used', false)->update(['is_used' => true]);

        $result = $this->sendOtpViaSms($mobile);

        if (! $result['success']) {
            return response()->json(['success' => false, 'message' => $result['message'] ?? 'Failed to resend OTP.'], 500);
        }

        Otp::create([
            'mobile'     => $mobile,
            'otp'        => $result['otp_code'] ?? '------',
            'is_used'    => false,
            'attempts'   => 0,
            'expires_at' => now()->addMinutes(10),
        ]);

        $message = ($result['demo'] ?? false) ? 'Demo OTP: 123456' : 'OTP resent successfully!';

        return response()->json([
            'success'    => true,
            'demo'       => $result['demo'] ?? false,
            'message'    => $message,
            'session_id' => $result['session_id'],
        ]);
    }

    // ── Determine where in the app to send the user after login ─────────────
    private function getRedirectTarget(User $user): string
    {
        if (! $user->name) {
            return 'personal_details';
        }

        $app = $user->latestApplication;

        if (! $app) {
            return 'dashboard';
        }

        return match (true) {
            in_array($app->status, ['payment_done', 'under_review', 'approved', 'disbursed', 'rejected']) => 'dashboard',
            $app->status === 'payment_pending' => 'unlock',
            in_array($app->status, ['eligibility_checked', 'loan_details_filled'])                        => 'pre_offer',
            default                                                                                        => 'dashboard',
        };
    }
}
