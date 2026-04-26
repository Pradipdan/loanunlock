<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\LoanApplication;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class PaymentController extends Controller
{
    private function currentUser()
    {
        return \App\Models\User::find(Session::get('user_id'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Show the unlock / payment page
    // ─────────────────────────────────────────────────────────────────────────
    public function showUnlock()
    {
        $user        = $this->currentUser();
        $application = $user->latestApplication;

        if (!$application || $application->status === 'draft') {
            return redirect()->route('application.pre_offer');
        }

        // Already paid → jump to status
        if (in_array($application->status, ['payment_done', 'under_review', 'approved', 'disbursed'])) {
            return redirect()->route('application.status');
        }

        $razorpayKey = config('services.razorpay.key');

        return view('user.unlock', compact('user', 'application', 'razorpayKey'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Create a Razorpay Order
    // ─────────────────────────────────────────────────────────────────────────
    public function initiatePayment(Request $request)
    {
        $user        = $this->currentUser();
        $application = $user->latestApplication;

        if (!$application) {
            return redirect()->route('application.eligibility');
        }

        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        $amount     = (int) config('app.processing_fee', 299);   // in INR
        $amountPaisa = $amount * 100;                              // Razorpay uses paise

        try {
            $orderData = [
                'receipt'         => 'rcpt_' . time() . '_' . $user->id,
                'amount'          => $amountPaisa,
                'currency'        => 'INR',
                'payment_capture' => 1 // Auto capture
            ];

            $razorpayOrder = $api->order->create($orderData);

            // Create a pending Payment record and store the Razorpay order ID
            $payment = Payment::create([
                'user_id'             => $user->id,
                'loan_application_id' => $application->id,
                'amount'              => $amount,
                'method'              => 'razorpay',
                'status'              => 'pending',
                'payment_gateway_id'  => $razorpayOrder['id'],
            ]);

            $application->update(['status' => 'payment_pending']);

            // Store in session
            Session::put('pending_payment_id', $payment->id);

            // Return order details for the frontend popup
            return response()->json([
                'order_id' => $razorpayOrder['id'],
                'amount'   => $amountPaisa,
                'key'      => config('services.razorpay.key'),
                'name'     => $user->name,
                'email'    => $user->email,
                'mobile'   => $user->mobile,
            ]);

        } catch (\Exception $e) {
            Log::error('Razorpay Order creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Payment initiation failed.'], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Verify Razorpay Signature after payment
    // ─────────────────────────────────────────────────────────────────────────
    public function verifyPayment(Request $request)
    {
        $user = $this->currentUser();
        $api  = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        try {
            $attributes = [
                'razorpay_order_id'   => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature'  => $request->razorpay_signature
            ];

            $api->utility->verifyPaymentSignature($attributes);

            // Success! Update payment
            $payment = Payment::where('payment_gateway_id', $request->razorpay_order_id)->first();
            
            if ($payment) {
                $payment->update([
                    'status'             => 'success',
                    'payment_gateway_id' => $request->razorpay_payment_id, // Store actual payment ID now
                    'paid_at'            => now(),
                    'method'             => 'razorpay',
                ]);

                if ($payment->loanApplication) {
                    $payment->loanApplication->update(['status' => 'under_review']);
                }

                Session::forget('pending_payment_id');
                return response()->json(['status' => 'success']);
            }

            return response()->json(['error' => 'Payment record not found'], 404);

        } catch (SignatureVerificationError $e) {
            Log::error('Razorpay Signature Verification failed: ' . $e->getMessage());
            return response()->json(['error' => 'Payment verification failed.'], 400);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Razorpay redirects here after successful payment UI (Frontend will handle redirect)
    // ─────────────────────────────────────────────────────────────────────────
    public function paymentSuccess(Request $request)
    {
        $user = $this->currentUser();
        $payment = Payment::where('user_id', $user->id)->where('status', 'success')->latest()->first();
        
        return view('user.payment_success', compact('user', 'payment'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Razorpay Webhook
    // ─────────────────────────────────────────────────────────────────────────
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sig     = $request->header('X-Razorpay-Signature');
        $secret  = config('services.razorpay.webhook_secret');

        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        try {
            $api->utility->verifyWebhookSignature($payload, $sig, $secret);
            
            $data = json_decode($payload, true);
            $event = $data['event'];

            if ($event === 'payment.captured') {
                $paymentId = $data['payload']['payment']['entity']['id'];
                $orderId   = $data['payload']['payment']['entity']['order_id'];

                $payment = Payment::where('payment_gateway_id', $orderId)->first();
                if ($payment && $payment->status !== 'success') {
                    $payment->update([
                        'status'  => 'success',
                        'paid_at' => now(),
                        'payment_gateway_id' => $paymentId,
                    ]);

                    if ($payment->loanApplication) {
                        $payment->loanApplication->update(['status' => 'under_review']);
                    }
                }
            }

        } catch (\Exception $e) {
            Log::warning('Razorpay webhook signature mismatch: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        return response()->json(['status' => 'ok']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Cosmofeed Initiation
    // ─────────────────────────────────────────────────────────────────────────
    public function initiateCosmofeed(Request $request)
    {
        $user        = $this->currentUser();
        $application = $user->latestApplication;

        if (!$application) {
            return redirect()->route('application.eligibility');
        }

        $amount = (int) config('app.processing_fee', 299);
        $txnId  = 'CF_' . time() . '_' . $user->id;

        // Create pending payment
        $payment = Payment::create([
            'user_id'             => $user->id,
            'loan_application_id' => $application->id,
            'amount'              => $amount,
            'method'              => 'cosmofeed', // CC/NetBanking via Cosmofeed
            'status'              => 'pending',
            'payment_gateway_id'  => $txnId,
        ]);

        $application->update(['status' => 'payment_pending']);
        Session::put('pending_payment_id', $payment->id);

        $baseUrl = env('COSMOFEED_URL', 'https://cosmofeed.com/vp/65a994xxxxx'); // Example URL

        $params = [
            'email'        => $user->email,
            'full_name'    => $user->name,
            'phone'        => $user->mobile,
            'redirect_url' => route('payment.success') . '?txn_id=' . $txnId,
        ];

        return redirect($baseUrl . '?' . http_build_query($params));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Cosmofeed Webhook
    // ─────────────────────────────────────────────────────────────────────────
    public function webhookCosmofeed(Request $request)
    {
        // Log all incoming webhooks for debugging
        Log::info('Cosmofeed Webhook received: ' . json_encode($request->all()));

        $payload = $request->all();
        $event   = $payload['event'] ?? '';
        $data    = $payload['data'] ?? [];

        // Cosmofeed usually sends 'payment.succeeded' or similar
        if ($event === 'payment.succeeded' || isset($payload['transactionId'])) {
            $txnId = $data['custom_field'] ?? $payload['custom_field'] ?? null; // Adjust based on how you pass it

            $payment = Payment::where('payment_gateway_id', $txnId)->first();
            
            if (!$payment) {
                // Fallback attempt to find by email/amount if no custom ID
                $email = $data['email'] ?? $payload['customer_email'] ?? null;
                $payment = Payment::where('status', 'pending')
                                  ->whereHas('user', function($query) use ($email) { 
                                      $query->where('email', $email); 
                                  })
                                  ->latest()
                                  ->first();
            }

            if ($payment && $payment->status !== 'success') {
                $payment->update([
                    'status'  => 'success',
                    'paid_at' => now(),
                    'gateway_response' => json_encode($payload),
                ]);

                if ($payment->loanApplication) {
                    $payment->loanApplication->update(['status' => 'under_review']);
                    Log::info("Payment #{$payment->id} (Cosmofeed) marked successful.");
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
