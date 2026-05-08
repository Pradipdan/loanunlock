<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class StorePaymentController extends Controller
{
    // ─── Create Razorpay order for store purchase (public, no auth) ──────
    public function createOrder(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:200',
        ]);

        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        $amount      = 299;          // INR
        $amountPaise = $amount * 100; // Razorpay uses paise

        try {
            $razorpayOrder = $api->order->create([
                'receipt'         => 'store_' . time() . '_' . rand(1000, 9999),
                'amount'          => $amountPaise,
                'currency'        => 'INR',
                'payment_capture' => 1,
                'notes'           => [
                    'product'  => $request->product_name,
                    'channel'  => 'smart_store',
                ],
            ]);

            return response()->json([
                'order_id' => $razorpayOrder['id'],
                'amount'   => $amountPaise,
                'key'      => config('services.razorpay.key'),
            ]);

        } catch (\Exception $e) {
            Log::error('Store Razorpay order failed: ' . $e->getMessage());
            return response()->json(['error' => 'Payment initiation failed. Please try again.'], 500);
        }
    }

    // ─── Verify Razorpay signature after payment ────────────────────────
    public function verifyOrder(Request $request)
    {
        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature'  => $request->razorpay_signature,
            ]);

            Log::info('Store payment verified: ' . $request->razorpay_payment_id . ' for product: ' . $request->product_name);

            return response()->json(['status' => 'success', 'payment_id' => $request->razorpay_payment_id]);

        } catch (SignatureVerificationError $e) {
            Log::error('Store payment verification failed: ' . $e->getMessage());
            return response()->json(['error' => 'Payment verification failed.'], 400);
        }
    }
}
