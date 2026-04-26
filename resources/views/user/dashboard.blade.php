@extends('layouts.app')
@section('title', 'My Dashboard')
@section('content')
<div style="background: linear-gradient(135deg, var(--blue) 0%, #5B7FE8 100%); padding: 28px 20px 50px; color:#fff;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
        <div>
            <div style="font-size:13px;opacity:.75;">Welcome back,</div>
            <div style="font-size:22px;font-weight:800;margin-top:2px;">{{ $user->name ?? 'User' }}</div>
            <div style="font-size:13px;opacity:.7;margin-top:4px;">+91 {{ $user->masked_mobile }}</div>
        </div>
        <a href="{{ route('logout') }}" style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);color:#fff;padding:8px 14px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;">Logout</a>
    </div>
</div>

<div class="app-content" style="margin-top:-28px;flex:1;">
    {{-- Active Loan Card --}}
    @if($latestApp)
    @php
        $statusColor = match($latestApp->status) {
            'approved','disbursed'  => 'var(--green)',
            'rejected'              => 'var(--red)',
            'under_review'          => 'var(--blue)',
            'payment_pending'       => 'var(--orange)',
            default                 => 'var(--blue)',
        };
    @endphp

    {{-- ⚠️ Payment Pending Banner --}}
    @if($latestApp->status === 'payment_pending')
    <div style="background:linear-gradient(135deg,#FFFAEB,#FFF3D6);border:2px solid #FEC84B;border-radius:20px;padding:20px;box-shadow:0 4px 24px rgba(0,0,0,.08);margin-bottom:16px;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
            <div style="width:44px;height:44px;background:linear-gradient(135deg,#f09210,#f7c948);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;box-shadow:0 4px 12px rgba(240,146,16,.35);">🔒</div>
            <div>
                <div style="font-weight:800;font-size:15px;color:#92400E;">Payment Pending</div>
                <div style="font-size:12px;color:#B45309;">Your processing fee of ₹299 is not yet received</div>
            </div>
        </div>
        <p style="font-size:13px;color:#78350F;line-height:1.6;margin-bottom:14px;">
            Your loan offer of <strong>₹{{ number_format($latestApp->approved_amount ?? 0) }}</strong> is pre-approved and waiting!
            Complete the ₹299 processing fee to proceed.
        </p>
        <a href="{{ route('payment.unlock') }}" style="display:block;width:100%;padding:14px;background:linear-gradient(135deg,#f09210,#f7c948);color:#1a1a1a;border-radius:14px;font-weight:800;font-size:15px;text-align:center;text-decoration:none;box-shadow:0 4px 16px rgba(240,146,16,.4);">
            Complete Payment — ₹299 🚀
        </a>
    </div>
    @endif

    <div style="background:#fff;border-radius:20px;padding:20px;box-shadow:0 4px 24px rgba(0,0,0,.08);margin-bottom:20px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
            <div style="font-weight:700;font-size:15px;">Active Application</div>
            <span style="background:{{ $statusColor }}1A;color:{{ $statusColor }};font-size:12px;font-weight:700;padding:4px 10px;border-radius:99px;">
                {{ ucwords(str_replace('_',' ',$latestApp->status)) }}
            </span>
        </div>
        <div style="font-size:11px;color:var(--gray-400);font-family:monospace;margin-bottom:12px;">{{ $latestApp->application_id }}</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div><div style="font-size:11px;color:var(--gray-400);">Loan Amount</div><div style="font-weight:800;font-size:18px;color:var(--blue);">₹{{ number_format($latestApp->approved_amount ?? $latestApp->requested_amount ?? 0) }}</div></div>
            @if($latestApp->emi_amount)
            <div><div style="font-size:11px;color:var(--gray-400);">Monthly EMI</div><div style="font-weight:700;font-size:16px;">₹{{ number_format($latestApp->emi_amount) }}</div></div>
            @endif
        </div>
        <a href="{{ route('application.status') }}" class="btn-outline" style="margin-top:14px;padding:11px;">View Details →</a>
    </div>
    @else
    <div style="background:#fff;border-radius:20px;padding:28px;box-shadow:0 4px 24px rgba(0,0,0,.08);margin-bottom:20px;text-align:center;">
        <div style="font-size:44px;margin-bottom:12px;">🏦</div>
        <div style="font-weight:700;font-size:17px;margin-bottom:6px;">Apply for a Loan</div>
        <div style="font-size:13px;color:var(--gray-600);margin-bottom:18px;">Get up to ₹5,00,000 instantly at best rates</div>
        <a href="{{ route('application.personal') }}" class="btn-primary">Apply Now →</a>
    </div>
    @endif

    {{-- Quick Stats --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;">
        <div style="background:#fff;border-radius:14px;padding:16px;box-shadow:0 2px 10px rgba(0,0,0,.05);">
            <div style="font-size:22px;margin-bottom:6px;">📋</div>
            <div style="font-size:22px;font-weight:800;color:var(--blue);">{{ $applications->count() }}</div>
            <div style="font-size:12px;color:var(--gray-600);margin-top:2px;">Total Applications</div>
        </div>
        <div style="background:#fff;border-radius:14px;padding:16px;box-shadow:0 2px 10px rgba(0,0,0,.05);">
            <div style="font-size:22px;margin-bottom:6px;">✅</div>
            <div style="font-size:22px;font-weight:800;color:var(--green);">{{ $applications->whereIn('status',['approved','disbursed'])->count() }}</div>
            <div style="font-size:12px;color:var(--gray-600);margin-top:2px;">Approved Loans</div>
        </div>
    </div>

    {{-- All Applications --}}
    @if($applications->count() > 0)
    <div style="font-weight:700;font-size:15px;margin-bottom:12px;">All Applications</div>
    @foreach($applications as $app)
    <a href="{{ route('application.status') }}" style="text-decoration:none;color:inherit;">
        <div style="background:#fff;border-radius:14px;padding:16px;box-shadow:0 2px 10px rgba(0,0,0,.05);margin-bottom:10px;display:flex;justify-content:space-between;align-items:center;">
            <div>
                <div style="font-weight:700;font-size:14px;">₹{{ number_format($app->approved_amount ?? $app->requested_amount ?? 0) }}</div>
                <div style="font-size:11px;color:var(--gray-400);font-family:monospace;margin-top:3px;">{{ $app->application_id }}</div>
                <div style="font-size:12px;color:var(--gray-600);margin-top:3px;">{{ $app->created_at->format('d M Y') }}</div>
            </div>
            <div style="text-align:right;">
                @php $sc = match($app->status){ 'approved','disbursed'=>'var(--green)','rejected'=>'var(--red)',default=>'var(--orange)'}; @endphp
                <span style="background:{{ $sc }}1A;color:{{ $sc }};font-size:11px;font-weight:700;padding:4px 10px;border-radius:99px;white-space:nowrap;">{{ ucwords(str_replace('_',' ',$app->status)) }}</span>
                <div style="font-size:12px;color:var(--gray-400);margin-top:6px;">{{ $app->tenure_months ? $app->tenure_months.' mo' : '' }}</div>
            </div>
        </div>
    </a>
    @endforeach
    @endif
</div>
@endsection
