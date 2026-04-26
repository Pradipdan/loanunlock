@extends('layouts.app')
@section('title', 'Application Status')
@section('content')
<div class="app-header">
    <a href="{{ route('user.dashboard') }}" class="btn-back"><i class="bi bi-arrow-left"></i></a>
    <div style="font-weight:700;font-size:15px;">Application Status</div>
</div>
<div class="app-content" style="flex:1;">
    {{-- Status Banner --}}
    @php
        $statusConfig = [
            'under_review' => ['color'=>'#175CD3','bg'=>'#EFF8FF','icon'=>'⏳','label'=>'Under Review'],
            'approved'     => ['color'=>'#027A48','bg'=>'#ECFDF3','icon'=>'✅','label'=>'Approved'],
            'rejected'     => ['color'=>'#B42318','bg'=>'#FEF3F2','icon'=>'❌','label'=>'Rejected'],
            'disbursed'    => ['color'=>'#027A48','bg'=>'#ECFDF3','icon'=>'💰','label'=>'Disbursed'],
        ];
        $cfg = $statusConfig[$application->status] ?? ['color'=>'#475467','bg'=>'#F9FAFB','icon'=>'📋','label'=>ucfirst($application->status)];
    @endphp

    <div style="background:{{ $cfg['bg'] }};border-radius:18px;padding:24px;text-align:center;margin-bottom:24px;">
        <div style="font-size:44px;margin-bottom:10px;">{{ $cfg['icon'] }}</div>
        <div style="font-size:20px;font-weight:800;color:{{ $cfg['color'] }};">{{ $cfg['label'] }}</div>
        <div style="font-size:13px;color:var(--gray-600);margin-top:6px;">
            @if($application->status === 'under_review') Your application is being reviewed by our team.
            @elseif($application->status === 'approved') Your loan has been approved! Disbursement in 2 business days.
            @elseif($application->status === 'rejected') We're sorry, your application was not approved at this time.
            @elseif($application->status === 'disbursed') Funds have been transferred to your account.
            @endif
        </div>
    </div>

    {{-- App Details Card --}}
    <div style="background:var(--gray-50);border-radius:16px;padding:20px;margin-bottom:20px;">
        <div style="font-weight:700;margin-bottom:14px;font-size:15px;">Loan Details</div>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <div style="display:flex;justify-content:space-between;font-size:14px;">
                <span style="color:var(--gray-600);">Application ID</span>
                <span style="font-weight:700;font-family:monospace;color:var(--blue);">{{ $application->application_id }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:14px;">
                <span style="color:var(--gray-600);">Loan Amount</span>
                <span style="font-weight:700;">₹{{ number_format($application->approved_amount ?? $application->requested_amount) }}</span>
            </div>
            @if($application->emi_amount)
            <div style="display:flex;justify-content:space-between;font-size:14px;">
                <span style="color:var(--gray-600);">Monthly EMI</span>
                <span style="font-weight:700;">₹{{ number_format($application->emi_amount) }}</span>
            </div>
            @endif
            @if($application->tenure_months)
            <div style="display:flex;justify-content:space-between;font-size:14px;">
                <span style="color:var(--gray-600);">Tenure</span>
                <span style="font-weight:600;">{{ $application->tenure_months }} Months</span>
            </div>
            @endif
            <div style="display:flex;justify-content:space-between;font-size:14px;">
                <span style="color:var(--gray-600);">Applied On</span>
                <span style="font-weight:600;">{{ $application->created_at->format('d M Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Rejection Reason --}}
    @if($application->status === 'rejected' && $application->rejection_reason)
    <div class="alert alert-error">
        <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;"></i>
        <div><strong>Reason for Rejection:</strong><br>{{ $application->rejection_reason }}</div>
    </div>
    @endif

    {{-- Progress Timeline --}}
    <div style="font-weight:700;margin-bottom:16px;font-size:15px;">Application Journey</div>
    @php
        $steps = [
            ['label'=>'Application Submitted','desc'=>'Your application was received','done'=>true,'time'=>$application->created_at->format('d M, h:i A')],
            ['label'=>'Payment Received','desc'=>'Processing fee of ₹299 collected','done'=>in_array($application->status,['payment_done','under_review','approved','rejected','disbursed']),'time'=>''],
            ['label'=>'Under Review','desc'=>'Being reviewed by our team','done'=>in_array($application->status,['under_review','approved','rejected','disbursed']),'active'=>$application->status==='under_review'],
            ['label'=>$application->status==='rejected'?'Application Rejected':'Loan Approved','desc'=>$application->status==='rejected'?$application->rejection_reason:'Loan terms finalized','done'=>in_array($application->status,['approved','rejected','disbursed']),'failed'=>$application->status==='rejected','time'=>$application->reviewed_at?->format('d M, h:i A')],
            ['label'=>'Funds Disbursed','desc'=>'Amount transferred to your account','done'=>$application->status==='disbursed','time'=>$application->disbursed_at?->format('d M, h:i A')],
        ];
    @endphp
    <ul class="status-timeline">
        @foreach($steps as $step)
        <li class="timeline-item">
            <div class="timeline-dot {{ isset($step['failed'])&&$step['failed'] ? 'failed' : (isset($step['active'])&&$step['active'] ? 'active' : ($step['done'] ? 'done' : 'pending')) }}">
                @if(isset($step['failed'])&&$step['failed']) ✕
                @elseif($step['done']) ✓
                @elseif(isset($step['active'])&&$step['active']) ●
                @else ○
                @endif
            </div>
            <div style="padding-top:6px;">
                <div style="font-weight:700;font-size:14px;">{{ $step['label'] }}</div>
                <div style="font-size:12px;color:var(--gray-600);margin-top:2px;">{{ $step['desc'] }}</div>
                @if(!empty($step['time']))
                    <div style="font-size:11px;color:var(--gray-400);margin-top:3px;">{{ $step['time'] }}</div>
                @endif
            </div>
        </li>
        @endforeach
    </ul>

    <div style="margin-top:8px; padding: 16px; background: var(--gray-50); border-radius:12px; font-size:13px; color:var(--gray-600); text-align:center;">
        Need help? Call <a href="tel:18001234567" style="color:var(--blue);font-weight:700;">1800-986-3452</a> or email <a href="mailto:support@loanunlock.com" style="color:var(--blue);font-weight:700;">support@loanunlock.com</a>
    </div>
</div>
@endsection
