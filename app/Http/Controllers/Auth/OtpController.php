<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class OtpController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // Generate a secure 6-digit OTP
    // ─────────────────────────────────────────────────────────────────────────
    private function generateOtp(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Send OTP — supports Fast2SMS, 2Factor.in, or demo mode
    // ─────────────────────────────────────────────────────────────────────────
    private function sendOtpViaSms(string $mobile): array
    {
        $provider = env('OTP_PROVIDER', 'demo');

        return match($provider) {
            'fast2sms' => $this->sendViaFast2Sms($mobile),
            '2factor'  => $this->sendVia2Factor($mobile),
            default    => $this->demoOtp(),
        };
    }

    // ─── Fast2SMS ─────────────────────────────────────────────────────────────
    private function sendViaFast2Sms(string $mobile): array
    {
        $apiKey = env('FAST2SMS_API_KEY');

        if (empty($apiKey) || $apiKey === 'your_fast2sms_api_key_here') {
            return $this->demoOtp();
        }

        $otp = $this->generateOtp();

        // Attempt 1: 'otp' route (works after website verification + first recharge)
        // Attempt 2: 'q'   route (quick/plain text, works after first recharge)
        // We use GET format as shown in Fast2SMS developer panel
        $routes = [
            [
                'route'            => 'otp',
                'variables_values' => $otp,
                'flash'            => 0,
                'numbers'          => $mobile,
            ],
            [
                'route'   => 'q',
                'message' => "Your First Smart Loan OTP is {$otp}. It is valid for 10 minutes. Do not share it.",
                'flash'   => 0,
                'numbers' => $mobile,
            ],
        ];

        foreach ($routes as $params) {
            try {
                $url = 'https://www.fast2sms.com/dev/bulkV2?' . http_build_query(
                    array_merge(['authorization' => $apiKey], $params)
                );

                $response = Http::timeout(10)->get($url);
                $body = $response->json();

                if (($body['return'] ?? false) === true) {
                    Log::info('Fast2SMS OTP sent via route=' . $params['route'] . ' to ' . $mobile);
                    return [
                        'success'    => true,
                        'session_id' => 'DB_VERIFY',
                        'otp_code'   => $otp,
                        'demo'       => false,
                    ];
                }

                $statusCode = $body['status_code'] ?? 0;

                // 996 = need website verification, 999 = need recharge — try next route
                if (in_array($statusCode, [996, 999])) {
                    Log::warning('Fast2SMS route=' . $params['route'] . ' unavailable (' . $statusCode . '): ' . json_encode($body['message'] ?? ''));
                    continue;
                }

                // Other errors — log and return
                Log::error('Fast2SMS send failed: ' . json_encode($body));
                $msg = is_array($body['message'] ?? null)
                    ? implode(', ', $body['message'])
                    : ($body['message'] ?? 'Failed to send OTP. Please try again.');
                return ['success' => false, 'message' => $msg];

            } catch (\Exception $e) {
                Log::error('Fast2SMS HTTP exception: ' . $e->getMessage());
            }
        }

        // All routes failed
        return ['success' => false, 'message' => 'SMS service is currently unavailable. Please try again shortly.'];
    }

    // ─── 2Factor.in ──────────────────────────────────────────────────────────
    private function sendVia2Factor(string $mobile): array
    {
        $apiKey = env('TWOFACTOR_API_KEY');

        if (empty($apiKey) || $apiKey === 'your_2factor_api_key_here') {
            return $this->demoOtp();
        }

        $otp = $this->generateOtp();

        try {
            $template = rawurlencode(env('TWOFACTOR_TEMPLATE', 'AUTOGEN'));
            $url = "https://2factor.in/API/V1/{$apiKey}/SMS/+91{$mobile}/{$otp}/{$template}";
            Log::info('2Factor SMS URL: ' . preg_replace('/V1\/[^\/]+\//', 'V1/***/', $url));

            $response = Http::timeout(10)->get($url);
            Log::info('2Factor SMS response: ' . $response->body());

            $body = $response->json();

            if (($body['Status'] ?? '') === 'Success') {
                return [
                    'success'    => true,
                    'session_id' => 'DB_VERIFY', // Verify locally from our DB
                    'otp_code'   => $otp,        // Store OTP in our DB so user can check
                    'demo'       => false,
                ];
            }

            $detail = $body['Details'] ?? 'Unknown error';
            Log::error('2Factor.in sendOtp failed: ' . $detail);

            $userMessage = match(true) {
                str_contains($detail, 'Insufficient') => 'SMS service temporarily unavailable. Please try again.',
                str_contains($detail, 'Invalid API')  => 'SMS configuration error. Please contact support.',
                str_contains($detail, 'Invalid Mobile') => 'Invalid mobile number. Please check and try again.',
                default => 'Failed to send OTP. Please try again.',
            };

            return ['success' => false, 'message' => $userMessage];

        } catch (\Exception $e) {
            Log::error('2Factor.in HTTP error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'SMS service unavailable. Please try again.'];
        }
    }

    // ─── Demo mode ────────────────────────────────────────────────────────────
    private function demoOtp(): array
    {
        return [
            'success'    => true,
            'session_id' => 'DEMO_SESSION',
            'otp_code'   => '123456',
            'demo'       => true,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Verify OTP — DB-based check (for Fast2SMS/demo) or 2Factor session
    // ─────────────────────────────────────────────────────────────────────────
    private function verifyOtpViaSms(string $sessionId, string $otpCode, ?string $storedOtp = null): bool
    {
        $provider = env('OTP_PROVIDER', 'demo');

        // Fast2SMS and demo: verify against DB-stored OTP
        if ($provider === 'fast2sms' || $sessionId === 'DEMO_SESSION' || $sessionId === 'DB_VERIFY') {
            return $storedOtp !== null && $storedOtp === $otpCode;
        }

        // 2Factor.in: verify via their API session
        $apiKey = env('TWOFACTOR_API_KEY');

        try {
            $response = Http::timeout(10)->get(
                "https://2factor.in/API/V1/{$apiKey}/SMS/VERIFY/{$sessionId}/{$otpCode}"
            );

            $body = $response->json();
            return ($body['Status'] ?? '') === 'Success' &&
                   ($body['Details'] ?? '') === 'OTP Matched';

        } catch (\Exception $e) {
            Log::error('2Factor.in verifyOtp error: ' . $e->getMessage());
            return false;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Show mobile number entry form
    // ─────────────────────────────────────────────────────────────────────────
    public function showMobile()
    {
        if (Session::get('user_id')) {
            return redirect()->route('user.dashboard');
        }
        return view('auth.mobile');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Send OTP to entered mobile
    // ─────────────────────────────────────────────────────────────────────────
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile'  => 'required|digits:10',
            'consent' => 'required|accepted',
        ], [
            'mobile.required'  => 'Mobile number is required.',
            'mobile.digits'    => 'Enter a valid 10-digit mobile number.',
            'consent.accepted' => 'Please accept the consent to continue.',
        ]);

        $mobile = $request->mobile;

        // Invalidate old DB OTP records (used for attempt tracking)
        Otp::where('mobile', $mobile)->where('is_used', false)->update(['is_used' => true]);

        // Send OTP
        $result = $this->sendOtpViaSms($mobile);

        if (!$result['success']) {
            return back()
                ->withInput()
                ->with('error', $result['message'] ?? 'Failed to send OTP. Please try again.');
        }

        // Store OTP record in DB for attempt tracking
        // In demo mode we store the code; in 2Factor mode we store the session_id
        Otp::create([
            'mobile'     => $mobile,
            'otp'        => $result['otp_code'] ?? '------', // not used for 2Factor verification
            'is_used'    => false,
            'attempts'   => 0,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Store session data
        Session::put('otp_mobile', $mobile);
        Session::put('otp_session_id', $result['session_id']);   // 2Factor session ID or DEMO_SESSION
        Session::put('otp_is_demo', $result['demo'] ?? false);

        $maskedMobile = '+91 ' . substr($mobile, 0, 2) . 'xxxxxx' . substr($mobile, -2);
        $message = ($result['demo'] ?? false)
            ? '⚠️ Demo mode — use OTP: 123456'
            : '✅ OTP sent via SMS to ' . $maskedMobile . '. Valid for 10 minutes.';

        return redirect()->route('otp.verify.form')->with('success', $message);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Show OTP entry form
    // ─────────────────────────────────────────────────────────────────────────
    public function showOtp()
    {
        if (!Session::get('otp_mobile')) {
            return redirect()->route('otp.mobile');
        }
        return view('auth.otp');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Verify entered OTP
    // ─────────────────────────────────────────────────────────────────────────
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $mobile    = Session::get('otp_mobile');
        $sessionId = Session::get('otp_session_id', 'DEMO_SESSION');

        if (!$mobile) {
            return redirect()->route('otp.mobile')->with('error', 'Session expired. Please try again.');
        }

        // Get the latest DB record for attempt tracking
        $otpRecord = Otp::where('mobile', $mobile)
            ->where('is_used', false)
            ->orderByDesc('created_at')
            ->first();

        if (!$otpRecord) {
            return back()->with('error', 'OTP expired. Please request a new one.');
        }

        // Check expiry
        if ($otpRecord->expires_at->isPast()) {
            $otpRecord->update(['is_used' => true]);
            return redirect()->route('otp.mobile')->with('error', 'OTP expired. Please request a new OTP.');
        }

        // Increment attempt count
        $otpRecord->increment('attempts');

        if ($otpRecord->attempts > 5) {
            $otpRecord->update(['is_used' => true]);
            return redirect()->route('otp.mobile')->with('error', 'Too many attempts. Please request a new OTP.');
        }

        // Verify — passes DB-stored OTP for Fast2SMS/demo local check
        $isValid = $this->verifyOtpViaSms($sessionId, $request->otp, $otpRecord->otp);

        if (!$isValid) {
            $remaining = 5 - $otpRecord->attempts;
            return back()->with('error', "Incorrect OTP. {$remaining} attempts left.");
        }

        // ✅ OTP verified successfully
        $otpRecord->update(['is_used' => true]);

        // Clear OTP session data
        Session::forget(['otp_mobile', 'otp_session_id', 'otp_is_demo']);

        // Create or find user
        $user = User::firstOrCreate(
            ['mobile' => $mobile],
            ['is_verified' => true]
        );
        $user->update(['is_verified' => true]);

        Session::put('user_id', $user->id);
        Session::put('user_mobile', $mobile);

        // ── Smart redirect based on application state ────────────────────────
        if (!$user->name) {
            return redirect()->route('application.personal');
        }

        $app = $user->latestApplication;

        if (!$app) {
            return redirect()->route('user.dashboard');
        }

        // Already paid / in review / approved → dashboard
        if (in_array($app->status, ['payment_done', 'under_review', 'approved', 'disbursed', 'rejected'])) {
            return redirect()->route('user.dashboard');
        }

        // Payment started but not completed (Razorpay cancelled etc.) → re-show unlock
        if ($app->status === 'payment_pending') {
            return redirect()->route('payment.unlock');
        }

        // Not yet reached the payment wall → show their pre-approved offer
        if (in_array($app->status, ['eligibility_checked', 'loan_details_filled'])) {
            return redirect()->route('application.pre_offer');
        }

        return redirect()->route('user.dashboard');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Resend OTP
    // ─────────────────────────────────────────────────────────────────────────
    public function resendOtp(Request $request)
    {
        $mobile = Session::get('otp_mobile');

        if (!$mobile) {
            return response()->json(['success' => false, 'message' => 'Session expired.']);
        }

        // Rate limit: max 3 OTPs per 15 minutes
        $recentCount = Otp::where('mobile', $mobile)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->count();

        if ($recentCount >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Too many OTP requests. Try after 15 minutes.',
            ]);
        }

        // Invalidate old
        Otp::where('mobile', $mobile)->where('is_used', false)->update(['is_used' => true]);

        // Send new OTP
        $result = $this->sendOtpViaSms($mobile);

        if (!$result['success']) {
            return response()->json(['success' => false, 'message' => $result['message'] ?? 'Failed to resend OTP.']);
        }

        // Save new record
        Otp::create([
            'mobile'     => $mobile,
            'otp'        => $result['otp_code'] ?? '------',
            'is_used'    => false,
            'attempts'   => 0,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Update session with new 2Factor session ID
        Session::put('otp_session_id', $result['session_id']);
        Session::put('otp_is_demo', $result['demo'] ?? false);

        $message = ($result['demo'] ?? false)
            ? 'Demo OTP: 123456'
            : 'OTP resent successfully!';

        return response()->json(['success' => true, 'message' => $message]);
    }
}
