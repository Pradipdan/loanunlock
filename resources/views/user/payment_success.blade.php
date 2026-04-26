@extends('layouts.app')
@section('title', 'Payment Successful')
@section('content')
<div class="app-content" style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding-top:60px;">
    <div class="success-icon">✓</div>
    <h1 style="font-size:26px;font-weight:800;margin-bottom:8px;">Payment Successful!</h1>
    <p style="color:var(--gray-600);font-size:14px;margin-bottom:32px;max-width:280px;">Your ₹299 processing fee has been received. Your application is now under review.</p>

    @if($payment)
    <div style="background:var(--gray-50);border-radius:16px;padding:20px;width:100%;margin-bottom:28px;text-align:left;">
        <div style="font-weight:700;margin-bottom:14px;font-size:15px;">Payment Details</div>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <div style="display:flex;justify-content:space-between;font-size:14px;">
                <span style="color:var(--gray-600);">Transaction ID</span>
                <span style="font-weight:600;font-size:12px;font-family:monospace;">{{ $payment->transaction_id }}</span>
            </div>
            @if($payment->payment_gateway_id)
            <div style="display:flex;justify-content:space-between;font-size:14px;">
                <span style="color:var(--gray-600);">Payment ID</span>
                <span style="font-weight:600;font-size:11px;font-family:monospace;word-break:break-all;">{{ $payment->payment_gateway_id }}</span>
            </div>
            @endif
            <div style="display:flex;justify-content:space-between;font-size:14px;">
                <span style="color:var(--gray-600);">Amount Paid</span>
                <span style="font-weight:700;color:var(--green);">₹{{ number_format($payment->amount, 2) }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:14px;">
                <span style="color:var(--gray-600);">Method</span>
                <span style="font-weight:600;text-transform:uppercase;">{{ $payment->method ?? 'Online' }} (Razorpay)</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:14px;">
                <span style="color:var(--gray-600);">Date &amp; Time</span>
                <span style="font-weight:600;">{{ ($payment->paid_at ?? now())->format('d M Y, h:i A') }}</span>
            </div>
        </div>
    </div>
    @endif

    <div style="background:var(--blue-light);border-radius:16px;padding:20px;width:100%;margin-bottom:28px;text-align:left;">
        <div style="font-size:22px;margin-bottom:8px;">⏳</div>
        <div style="font-weight:700;font-size:15px;margin-bottom:6px;">What happens next?</div>
        <div style="font-size:13px;color:var(--gray-600);line-height:1.8;">
            <div>1️⃣ Our team reviews your application within <strong>24–48 hours</strong></div>
            <div>2️⃣ You'll get SMS &amp; email with the decision</div>
            <div>3️⃣ If approved, funds disbursed in <strong>2 business days</strong></div>
        </div>
    </div>

    <a href="{{ route('application.status') }}" class="btn-primary" style="width:100%;">View Application Status</a>
    <a href="{{ route('user.dashboard') }}" class="btn-outline" style="margin-top:10px;">Go to Dashboard</a>
</div>
@endsection
