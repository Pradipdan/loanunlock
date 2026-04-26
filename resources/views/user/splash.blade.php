@extends('layouts.app')
@section('title', 'Welcome')
@section('content')
<style>
    .splash-screen {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 40px 30px;
        background: #fff;
    }
    .splash-logo-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
        animation: fadeUp .8s ease both;
    }
    .splash-logo-icon {
        width: 90px; height: 90px;
        background: var(--blue);
        border-radius: 28px;
        display: flex; align-items: center; justify-content: center;
        font-size: 46px;
        box-shadow: 0 12px 32px rgba(59,91,219,0.35);
    }
    .splash-brand { font-size: 34px; font-weight: 800; color: var(--gray-900); }
    .splash-brand span { color: var(--blue); }
    .splash-tagline {
        font-size: 16px;
        font-style: italic;
        color: var(--gray-600);
        font-weight: 500;
        letter-spacing: .3px;
    }
    .splash-divider {
        width: 48px; height: 3px;
        background: var(--blue);
        border-radius: 99px;
        margin: 24px 0;
        animation: fadeUp .8s .2s ease both;
        opacity: 0;
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .splash-trust {
        width: 100%;
        animation: fadeUp .8s .4s ease both;
        opacity: 0;
    }
    .splash-cta {
        width: 100%;
        margin-top: 32px;
        animation: fadeUp .8s .6s ease both;
        opacity: 0;
    }
    .splash-loader {
        display: flex;
        gap: 6px;
        margin-top: 20px;
        justify-content: center;
    }
    .loader-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: var(--blue);
        animation: bounce 1.2s infinite;
    }
    .loader-dot:nth-child(2) { animation-delay: .2s; }
    .loader-dot:nth-child(3) { animation-delay: .4s; }
    @keyframes bounce {
        0%, 80%, 100% { transform: scale(0); opacity: .4; }
        40% { transform: scale(1); opacity: 1; }
    }
</style>

<div class="splash-screen">
    <div class="splash-logo-wrap">
        <div class="splash-logo-icon">🏦</div>
        <div>
            <div class="splash-brand">First Smart<span>Loan</span></div>
            <div class="splash-tagline">"Life me Load nahi, Loan le"</div>
        </div>
    </div>

    <div class="splash-divider"></div>

    <div class="splash-trust">
        <div class="trust-row">
            <div class="trust-item blue">
                <div class="icon-wrap"><i class="bi bi-people-fill"></i></div>
                <div>Trusted by<br>4Cr+ Users</div>
            </div>
            <div class="trust-item green">
                <div class="icon-wrap"><i class="bi bi-patch-check-fill"></i></div>
                <div>RBI Approved<br>Partners</div>
            </div>
            <div class="trust-item orange">
                <div class="icon-wrap"><i class="bi bi-phone-fill"></i></div>
                <div>100% Digital<br>Process</div>
            </div>
        </div>
    </div>

    <div class="splash-cta">
        <a href="{{ route('otp.mobile') }}" class="btn-primary">Get Started – Apply for Loan</a>
        <div class="splash-loader" id="autoLoader">
            <div class="loader-dot"></div>
            <div class="loader-dot"></div>
            <div class="loader-dot"></div>
        </div>
        <p style="text-align:center; font-size:12px; color: var(--gray-400); margin-top:10px;">Auto-redirecting in 3 seconds...</p>
    </div>
</div>

@push('scripts')
<script>
    setTimeout(() => {
        window.location.href = "{{ route('otp.mobile') }}";
    }, 3000);
</script>
@endpush
@endsection
