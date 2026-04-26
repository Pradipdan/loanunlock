@extends('layouts.app')
@section('title', 'Check Eligibility')
@section('content')

<div class="app-header">
    <a href="{{ route('application.loan') }}" class="btn-back"><i class="bi bi-arrow-left"></i></a>
    <div style="font-weight:700; font-size:15px;">Eligibility Check</div>
</div>

<div class="app-content" style="flex:1; padding-top:8px;">
    <div style="text-align:center; padding: 32px 0 24px;">
        <div style="font-size: 56px; margin-bottom:12px;">🔍</div>
        <h2 style="font-size:22px; font-weight:800;">Check Your Eligibility</h2>
        <p style="color: var(--gray-600); font-size:14px; margin-top:8px;">We'll analyze your profile and give you instant results.</p>
    </div>

    <div style="background: var(--gray-50); border-radius: 18px; padding: 20px; margin-bottom: 24px;">
        <div style="font-weight:700; margin-bottom:16px; font-size:15px;">Application Summary</div>
        <div style="display:flex; flex-direction:column; gap: 12px;">
            <div style="display:flex; justify-content:space-between;">
                <span style="color: var(--gray-600); font-size:14px;">Name</span>
                <span style="font-weight:600; font-size:14px;">{{ $user->name }}</span>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <span style="color: var(--gray-600); font-size:14px;">PAN</span>
                <span style="font-weight:600; font-size:14px;">{{ substr($user->pan_number, 0, 3) }}***{{ substr($user->pan_number, -2) }}</span>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <span style="color: var(--gray-600); font-size:14px;">Employment</span>
                <span style="font-weight:600; font-size:14px;">{{ ucfirst($user->employment_type ?? '-') }}</span>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <span style="color: var(--gray-600); font-size:14px;">Monthly Income</span>
                <span style="font-weight:600; font-size:14px;">₹{{ number_format($user->monthly_income) }}</span>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <span style="color: var(--gray-600); font-size:14px;">Loan Amount</span>
                <span style="font-weight:700; font-size:16px; color: var(--blue);">₹{{ number_format($application->requested_amount) }}</span>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <span style="color: var(--gray-600); font-size:14px;">Tenure</span>
                <span style="font-weight:600; font-size:14px;">{{ $application->tenure_months }} Months</span>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="bi bi-info-circle-fill" style="flex-shrink:0;"></i>
        Eligibility check uses a soft credit inquiry and will <strong>not</strong> affect your CIBIL score.
    </div>
</div>

<div class="sticky-bottom">
    <form action="{{ route('application.check.eligibility') }}" method="POST" id="eligForm">
        @csrf
        <button type="submit" class="btn-primary" id="eligBtn" onclick="startCheck()">
            <span id="eligText">🎯 Check My Eligibility</span>
            <span id="eligLoader" style="display:none;">Checking...</span>
        </button>
    </form>
</div>

@push('scripts')
<script>
function startCheck() {
    document.getElementById('eligText').style.display = 'none';
    document.getElementById('eligLoader').style.display = 'inline';
    document.getElementById('eligBtn').disabled = true;
}
</script>
@endpush
@endsection
