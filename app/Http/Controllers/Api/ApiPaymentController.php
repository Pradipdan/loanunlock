<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class ApiPaymentController extends Controller
{
    // ── POST /api/payment/initiate ───────────────────────────────────────────
    public function initiatePayment(Request $request)
    {
        $user        = $request->user();
        $application = $user->latestApplication;

        if (! $application) {
            return response()->json(['success' => false, 'message' => 'No active application found.'], 422);
        }

        if (in_array($application->status, ['payment_done', 'under_review', 'approved', 'disbursed'])) {
            return response()->json(['success' => false, 'message' => 'Payment already completed.'], 422);
        }

        $api         = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
        $amount      = (int) config('app.processing_fee', 299);
        $amountPaisa = $amount * 100;

        try {
            $orderData = [
                'receipt'         => 'rcpt_' . time() . '_' . $user->id,
                'amount'          => $amountPaisa,
                'currency'        => 'INR',
                'payment_capture' => 1,
            ];

            $razorpayOrder = $api->order->create($orderData);

            $payment = Payment::create([
                'user_id'             => $user->id,
                'loan_application_id' => $application->id,
                'amount'              => $amount,
                'method'              => 'razorpay',
                'status'              => 'pending',
                'payment_gateway_id'  => $razorpayOrder['id'],
            ]);

            $application->update(['status' => 'payment_pending']);

            return response()->json([
                'success'  => true,
                'order_id' => $razorpayOrder['id'],
                'amount'   => $amountPaisa,
                'currency' => 'INR',
                'key'      => config('services.razorpay.key'),
                'prefill'  => [
                    'name'    => $user->name ?? '',
                    'email'   => $user->email ?? '',
                    'contact' => '+91' . $user->mobile,
                ],
                'notes' => [
                    'application_id' => $application->application_id,
                    'payment_db_id'  => $payment->id,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Razorpay order creation failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Payment initiation failed. Please try again.'], 500);
        }
    }

    // ── POST /api/payment/verify ─────────────────────────────────────────────
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'razorpay_order_id'   => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature'  => 'required|string',
        ]);

        $user = $request->user();
        $api  = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature'  => $request->razorpay_signature,
            ]);

            $payment = Payment::where('payment_gateway_id', $request->razorpay_order_id)->first();

            if (! $payment) {
                return response()->json(['success' => false, 'message' => 'Payment record not found.'], 404);
            }

            $payment->update([
                'status'             => 'success',
                'payment_gateway_id' => $request->razorpay_payment_id,
                'paid_at'            => now(),
            ]);

            if ($payment->loanApplication) {
                $payment->loanApplication->update(['status' => 'under_review']);
            }

            return response()->json([
                'success'     => true,
                'message'     => 'Payment verified successfully!',
                'redirect_to' => 'payment_success',
            ]);
        } catch (SignatureVerificationError $e) {
            Log::error('Razorpay signature verification failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Payment verification failed.'], 400);
        }
    }

    // ── GET /api/payment/status ──────────────────────────────────────────────
    public function paymentStatus(Request $request)
    {
        $user    = $request->user();
        $payment = Payment::where('user_id', $user->id)
            ->where('status', 'success')
            ->latest()
            ->first();

        return response()->json([
            'success' => true,
            'payment' => $payment ? [
                'id'      => $payment->id,
                'amount'  => $payment->amount,
                'status'  => $payment->status,
                'paid_at' => $payment->paid_at?->toDateTimeString(),
                'method'  => $payment->method,
            ] : null,
        ]);
    }

    // ── POST /api/payment/razorpay-webhook ───────────────────────────────────
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sig     = $request->header('X-Razorpay-Signature');
        $secret  = config('services.razorpay.webhook_secret');

        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        try {
            $api->utility->verifyWebhookSignature($payload, $sig, $secret);

            $data  = json_decode($payload, true);
            $event = $data['event'] ?? '';

            if ($event === 'payment.captured') {
                $paymentId = $data['payload']['payment']['entity']['id'];
                $orderId   = $data['payload']['payment']['entity']['order_id'];

                $payment = Payment::where('payment_gateway_id', $orderId)->first();

                if ($payment && $payment->status !== 'success') {
                    $payment->update([
                        'status'             => 'success',
                        'paid_at'            => now(),
                        'payment_gateway_id' => $paymentId,
                    ]);

                    if ($payment->loanApplication) {
                        $payment->loanApplication->update(['status' => 'under_review']);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Razorpay webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        return response()->json(['status' => 'ok']);
    }
}
