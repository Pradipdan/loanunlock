@extends('layouts.app')
@section('title', 'Enter OTP')
@section('content')

<div class="app-content" style="padding-top: 40px; min-height: 100vh; display: flex; flex-direction: column; justify-content: space-between;">
    <div>
        <a href="{{ route('otp.mobile') }}" class="btn-back" style="display:inline-flex; margin-bottom: 24px;">
            <i class="bi bi-arrow-left"></i>
        </a>

        <div style="margin-bottom: 8px;">
            <div style="width: 56px; height: 56px; background: var(--blue-light); border-radius: 16px; display:flex; align-items:center; justify-content:center; font-size:28px; margin-bottom: 20px;">🔐</div>
            <h1 class="screen-title">Enter OTP</h1>
            <p class="screen-subtitle">
                We've sent a 6-digit OTP to your mobile number.
                @if(session('otp_mobile'))
                    <br><strong>+91 {{ substr(session('otp_mobile'), 0, 2) }}****{{ substr(session('otp_mobile'), -2) }}</strong>
                @endif
            </p>
        </div>

        @if(session('otp_is_demo'))
            <div style="background:linear-gradient(135deg,#FFFAEB,#FFF3D6);border:2px solid #FEC84B;border-radius:16px;padding:16px 18px;margin-bottom:20px;display:flex;gap:12px;align-items:flex-start;">
                <div style="font-size:24px;flex-shrink:0;">🧪</div>
                <div>
                    <div style="font-weight:800;font-size:14px;color:#92400E;margin-bottom:4px;">Demo Mode — No real SMS sent</div>
                    <div style="font-size:13px;color:#78350F;">Enter OTP: <strong style="font-size:22px;letter-spacing:6px;color:#1a237e;font-family:monospace;">123456</strong></div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error"><i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}</div>
        @endif

        <form action="{{ route('otp.verify') }}" method="POST" id="otpForm">
            @csrf
            <input type="hidden" name="otp" id="otpHidden">
            <div class="otp-inputs">
                <input type="text" class="otp-input" maxlength="1" inputmode="numeric" data-index="0">
                <input type="text" class="otp-input" maxlength="1" inputmode="numeric" data-index="1">
                <input type="text" class="otp-input" maxlength="1" inputmode="numeric" data-index="2">
                <input type="text" class="otp-input" maxlength="1" inputmode="numeric" data-index="3">
                <input type="text" class="otp-input" maxlength="1" inputmode="numeric" data-index="4">
                <input type="text" class="otp-input" maxlength="1" inputmode="numeric" data-index="5">
            </div>
            @error('otp')
                <div class="invalid-feedback" style="display:block; text-align:center; margin-top: -16px; margin-bottom: 16px;">{{ $message }}</div>
            @enderror

            <div style="text-align: center; margin-bottom: 24px; font-size: 14px; color: var(--gray-600);">
                Didn't receive OTP?
                <button type="button" id="resendBtn" style="background:none; border:none; color: var(--blue); font-weight:700; cursor:pointer; font-size:14px; padding:0;">
                    Resend OTP
                </button>
                <span id="timerText" style="color: var(--gray-400);">(wait <span id="countdown">30</span>s)</span>
            </div>

            <button type="submit" class="btn-primary" id="verifyBtn" disabled>Verify & Continue</button>
        </form>
    </div>

    <div class="secure-badge">
        <i class="bi bi-shield-fill-check"></i>
        Your data is 100% encrypted and secure with us.
    </div>
</div>

@push('scripts')
<script>
const inputs = document.querySelectorAll('.otp-input');
const verifyBtn = document.getElementById('verifyBtn');
const otpHidden = document.getElementById('otpHidden');

inputs.forEach((input, i) => {
    input.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
        if (this.value && i < inputs.length - 1) inputs[i + 1].focus();
        updateHidden();
        verifyBtn.disabled = getOTP().length !== 6;
    });
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && !this.value && i > 0) {
            inputs[i - 1].focus();
        }
    });
    input.addEventListener('paste', function(e) {
        const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'').slice(0, 6);
        paste.split('').forEach((c, j) => { if (inputs[j]) inputs[j].value = c; });
        updateHidden();
        verifyBtn.disabled = getOTP().length !== 6;
        e.preventDefault();
    });
});

function getOTP() { return [...inputs].map(i => i.value).join(''); }
function updateHidden() { otpHidden.value = getOTP(); }

// Timer
let seconds = 30;
const countdown = document.getElementById('countdown');
const resendBtn = document.getElementById('resendBtn');
const timerText = document.getElementById('timerText');
resendBtn.disabled = true;

const timer = setInterval(() => {
    seconds--;
    countdown.textContent = seconds;
    if (seconds <= 0) {
        clearInterval(timer);
        timerText.style.display = 'none';
        resendBtn.style.opacity = '1';
        resendBtn.disabled = false;
    }
}, 1000);

resendBtn.addEventListener('click', function() {
    fetch('{{ route("otp.resend") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
    }).then(r => r.json()).then(d => {
        alert(d.message);
    });
});

document.getElementById('otpForm').addEventListener('submit', function() {
    verifyBtn.textContent = 'Verifying...';
    verifyBtn.disabled = true;
});

inputs[0].focus();
</script>
@endpush
@endsection
