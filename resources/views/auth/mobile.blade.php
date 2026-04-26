@extends('layouts.app')
@section('title', 'Verify Mobile')
@section('content')

<div class="app-content" style="padding-top: 40px; min-height: 100vh; display: flex; flex-direction: column; justify-content: space-between;">
    <div>
        <div style="margin-bottom: 32px;">
            <div style="width: 56px; height: 56px; background: var(--blue-light); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 20px;">📱</div>
            <h1 class="screen-title">Verify your phone number</h1>
            <p class="screen-subtitle">We'll send an OTP to keep your account secure.</p>
        </div>

        @if(session('error'))
            <div class="alert alert-error"><i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
        @endif

        <form action="{{ route('otp.send') }}" method="POST" id="mobileForm">
            @csrf
            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <div class="phone-input-wrap @error('mobile') is-invalid @enderror">
                    <span class="phone-code">🇮🇳 +91</span>
                    <input type="tel" name="mobile" class="phone-input" placeholder="Enter mobile number"
                           maxlength="10" pattern="[0-9]{10}"
                           value="{{ old('mobile') }}" autofocus inputmode="numeric">
                </div>
                @error('mobile')
                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                @enderror
            </div>

            <div class="consent-wrap">
                <input type="checkbox" name="consent" id="consent" value="1" {{ old('consent') ? 'checked' : '' }}>
                <div class="consent-text">
                    I give consent to <strong>Roctogen Services Private Limited (First Smart Loan)</strong> sharing my personal and loan-related details (including credit information) with lending partners, bureaus, and other institutions as required. I agree to the
                    <a href="#">Terms & Conditions</a> and <a href="#">Privacy Policy</a>.
                </div>
            </div>
            @error('consent')
                <div class="invalid-feedback" style="display:block; margin-top: -12px; margin-bottom: 12px;">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn-primary" id="sendBtn">
                <span id="btnText">Send OTP</span>
                <span id="btnLoader" style="display:none;">Sending...</span>
            </button>
        </form>
    </div>

    <div class="secure-badge">
        <i class="bi bi-shield-fill-check"></i>
        Your data is 100% encrypted and secure with us.
    </div>
</div>

@push('scripts')
<script>
    // Only allow digits
    document.querySelector('.phone-input').addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 10);
    });

    document.getElementById('mobileForm').addEventListener('submit', function() {
        document.getElementById('btnText').style.display = 'none';
        document.getElementById('btnLoader').style.display = 'inline';
    });
</script>
@endpush
@endsection
