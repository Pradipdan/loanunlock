@extends('layouts.app')
@section('title', 'Your Pre-Approved Offer')

@push('styles')
<style>
    /* ─── Animated gradient header ─── */
    .offer-hero {
        background: linear-gradient(135deg, #1a237e 0%, #3B5BDB 45%, #5c6bc0 100%);
        padding: 32px 24px 28px;
        color: #fff;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .offer-hero::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 200px; height: 200px;
        background: rgba(255,255,255,.06);
        border-radius: 50%;
    }
    .offer-hero::after {
        content: '';
        position: absolute;
        bottom: -40px; left: -40px;
        width: 140px; height: 140px;
        background: rgba(255,255,255,.05);
        border-radius: 50%;
    }

    /* ─── Confetti burst ─── */
    @keyframes confettiFall {
        0%   { transform: translateY(-20px) rotate(0deg); opacity: 1; }
        100% { transform: translateY(60px)  rotate(720deg); opacity: 0; }
    }
    .confetti-dot {
        position: absolute;
        width: 7px; height: 7px;
        border-radius: 2px;
        animation: confettiFall 1.6s ease-out forwards;
    }

    /* ─── Ticker lines ─── */
    @keyframes slideUp {
        from { transform: translateY(12px); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
    }
    .offer-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,.15);
        border: 1px solid rgba(255,255,255,.25);
        border-radius: 99px;
        padding: 5px 14px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .5px;
        text-transform: uppercase;
        margin-bottom: 16px;
        animation: slideUp .5s .1s both;
    }
    .blink-dot {
        width: 7px; height: 7px;
        background: #69f0ae;
        border-radius: 50%;
        animation: blink 1s infinite;
    }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }

    .hero-label {
        font-size: 13px;
        opacity: .75;
        font-weight: 500;
        margin-bottom: 4px;
        animation: slideUp .45s .15s both;
    }
    .hero-name {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 18px;
        animation: slideUp .45s .2s both;
    }

    /* ─── Requested vs Pre-approved strip ─── */
    .amount-compare {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        animation: slideUp .5s .3s both;
        margin-bottom: 20px;
    }
    .amt-box {
        flex: 1;
        background: rgba(255,255,255,.12);
        border-radius: 14px;
        padding: 14px 10px;
        text-align: center;
    }
    .amt-box-label {
        font-size: 11px;
        opacity: .7;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: .4px;
    }
    .amt-box-value {
        font-size: 22px;
        font-weight: 800;
        margin-top: 4px;
        letter-spacing: -.5px;
    }
    .amt-box.highlight {
        background: rgba(255,255,255,.22);
        border: 1.5px solid rgba(255,255,255,.4);
        position: relative;
    }
    .amt-box.highlight .amt-box-value {
        font-size: 28px;
        color: #ffd54f;
    }
    .more-badge {
        position: absolute;
        top: -10px;
        left: 50%;
        transform: translateX(-50%);
        background: #ffd54f;
        color: #1a237e;
        border-radius: 99px;
        padding: 2px 10px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .3px;
        white-space: nowrap;
    }
    .arrow-icon {
        font-size: 22px;
        opacity: .6;
        flex-shrink: 0;
    }

    /* ─── Credit score pill ─── */
    .score-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255,255,255,.13);
        border-radius: 99px;
        padding: 7px 16px;
        font-size: 13px;
        font-weight: 600;
        animation: slideUp .5s .4s both;
    }
    .score-pill strong { color: #69f0ae; font-size: 16px; }

    /* ─── Lock card ─── */
    .lock-card {
        background: linear-gradient(135deg, #fff7e6 0%, #fff3d6 100%);
        border: 2px solid #fec84b;
        border-radius: 20px;
        padding: 22px 20px;
        margin: 20px 20px 0;
        position: relative;
        overflow: hidden;
    }
    .lock-card::before {
        content: '🔒';
        position: absolute;
        bottom: -10px; right: -10px;
        font-size: 80px;
        opacity: .06;
    }
    .lock-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
    }
    .lock-icon-wrap {
        width: 46px; height: 46px;
        background: linear-gradient(135deg, #f09210, #f7c948);
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(240,146,16,.35);
    }
    .lock-title { font-weight: 800; font-size: 16px; color: #92400e; }
    .lock-subtitle { font-size: 12px; color: #b45309; margin-top: 2px; }

    .lock-body {
        font-size: 14px;
        color: #78350f;
        line-height: 1.7;
        margin-bottom: 16px;
    }
    .lock-body strong { color: #92400e; }

    /* ─── What's unlocked list ─── */
    .unlock-list {
        list-style: none;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 18px;
    }
    .unlock-list li {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13.5px;
        font-weight: 600;
        color: #451a03;
    }
    .unlock-list li .check-ico {
        width: 26px; height: 26px;
        background: #fef3c7;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
    }

    /* ─── Trust tiles ─── */
    .trust-tiles {
        display: flex;
        gap: 8px;
        margin-bottom: 18px;
    }
    .trust-tile {
        flex: 1;
        background: #fff;
        border-radius: 12px;
        padding: 10px 6px;
        text-align: center;
        font-size: 11.5px;
        font-weight: 700;
        border: 1px solid #fde68a;
    }
    .trust-tile .t-icon { font-size: 18px; display: block; margin-bottom: 4px; }
    .trust-tile .t-label { color: #92400e; }

    /* ─── Price row ─── */
    .price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
        border-radius: 14px;
        padding: 14px 18px;
        border: 1.5px solid #fde68a;
        margin-bottom: 16px;
    }
    .price-label { font-weight: 600; font-size: 14px; color: #78350f; }
    .price-label span { display: block; font-size: 11.5px; color: #b45309; font-weight: 500; }
    .price-value { font-size: 32px; font-weight: 900; color: var(--blue); letter-spacing: -1px; }

    /* ─── Pay button ─── */
    .btn-pay {
        display: block;
        width: 100%;
        padding: 18px;
        background: linear-gradient(135deg, #f09210 0%, #f7c948 100%);
        color: #1a1a1a;
        border: none;
        border-radius: 16px;
        font-family: inherit;
        font-size: 16px;
        font-weight: 800;
        text-align: center;
        cursor: pointer;
        transition: transform .1s, box-shadow .2s;
        box-shadow: 0 6px 20px rgba(240,146,16,.45);
        letter-spacing: .2px;
    }
    .btn-pay:hover  { box-shadow: 0 8px 28px rgba(240,146,16,.55); }
    .btn-pay:active { transform: scale(.97); }

    /* ─── Payment method pills ─── */
    .method-pills {
        display: flex;
        gap: 8px;
        margin-bottom: 14px;
    }
    .method-pill {
        flex: 1;
        border: 1.5px solid var(--gray-200);
        border-radius: 12px;
        padding: 10px 6px;
        text-align: center;
        cursor: pointer;
        transition: all .2s;
        font-size: 12px;
        font-weight: 600;
        color: var(--gray-600);
    }
    .method-pill input[type=radio] { display: none; }
    .method-pill:has(input:checked) {
        border-color: var(--blue);
        background: var(--blue-light);
        color: var(--blue);
    }
    .method-pill .m-icon { font-size: 20px; display: block; margin-bottom: 5px; }

    /* ─── Shimmer number reveal ─── */
    @keyframes shimmerReveal {
        0%   { background-position: -400px 0; }
        100% { background-position: 400px 0; }
    }
    .amount-shimmer {
        display: inline-block;
        background: linear-gradient(90deg, rgba(255,255,255,.25) 0%, rgba(255,255,255,.55) 50%, rgba(255,255,255,.25) 100%);
        background-size: 400px 100%;
        animation: shimmerReveal 1.4s infinite;
        border-radius: 8px;
        color: transparent;
        user-select: none;
    }

    /* ─── Urgency banner ─── */
    .urgency-bar {
        background: linear-gradient(90deg, #1a237e, #3B5BDB);
        color: #fff;
        text-align: center;
        padding: 8px 20px;
        font-size: 12.5px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    #countdown { font-weight: 800; color: #ffd54f; }

    /* ─── EMI preview pill ─── */
    .emi-preview {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--blue-light);
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 14px;
    }
    .emi-label { font-size: 12px; color: var(--blue); font-weight: 600; }
    .emi-value { font-size: 18px; font-weight: 800; color: var(--blue); }
    .emi-note  { font-size: 10.5px; color: var(--gray-400); margin-top: 1px; }
</style>
@endpush

@section('content')

{{-- ── Urgency: countdown timer ───────────────────────────────────────────── --}}
<div class="urgency-bar">
    ⏳ Offer reserved for <span id="countdown">14:59</span> · Don't miss this!
</div>

{{-- ── Hero: congrats + amount comparison ──────────────────────────────────── --}}
<div class="offer-hero" id="confettiZone">
    {{-- live dot --}}
    <div class="offer-tag">
        <span class="blink-dot"></span>
        Pre-Approved Offer
    </div>

    <div class="hero-label">Congratulations</div>
    <div class="hero-name">{{ $user->name ?? 'there' }} 🎉</div>

    {{-- Requested vs Pre-approved --}}
    <div class="amount-compare">
        <div class="amt-box">
            <div class="amt-box-label">You asked for</div>
            <div class="amt-box-value">₹{{ number_format($application->requested_amount, 0) }}</div>
        </div>
        <div class="arrow-icon">→</div>
        <div class="amt-box highlight">
            <div class="more-badge">⬆ {{
                round((($application->approved_amount - $application->requested_amount) / $application->requested_amount) * 100)
            }}% MORE</div>
            <div class="amt-box-label">Pre-Approved for</div>
            <div class="amt-box-value">₹{{ number_format($application->approved_amount, 0) }}</div>
        </div>
    </div>

    {{-- Credit score --}}
    <div class="score-pill">
        <i class="bi bi-graph-up-arrow"></i>
        {{ $application->bureau_name ?? 'CIBIL' }} Score: <strong>{{ $application->credit_score }}</strong>
        &nbsp;·&nbsp; {{ $application->credit_score > 750 ? 'Excellent' : 'Good' }} ✅
    </div>

    {{-- floating confetti (injected by JS) --}}
</div>

{{-- ── EMI preview strip ────────────────────────────────────────────────────── --}}
<div style="padding: 0 20px; margin-top: 16px;">
    <div class="emi-preview">
        <div>
            <div class="emi-label">Monthly EMI</div>
            <div class="emi-note">₹{{ number_format($application->approved_amount, 0) }} · {{ $application->tenure_months }} months</div>
        </div>
        <div class="emi-value">₹{{ number_format($application->emi_amount, 0) }}/mo</div>
    </div>
</div>

{{-- ── Payment wall card ───────────────────────────────────────────────────── --}}
<div class="lock-card">
    <div class="lock-card-header">
        <div class="lock-icon-wrap">🔒</div>
        <div>
            <div class="lock-title">Unlock Your Full Offer</div>
            <div class="lock-subtitle">Pay a one-time processing fee to proceed</div>
        </div>
    </div>

    <div class="lock-body">
        Your pre-approved loan of <strong>₹{{ number_format($application->approved_amount, 0) }}</strong>
        at <strong>18% p.a.</strong> is ready — just pay a small one-time fee to view your
        <strong>complete loan offer</strong>, e-sign, and get funds in your account within
        <strong>24–48 hours</strong>.
    </div>

    {{-- What you unlock --}}
    <ul class="unlock-list">
        <li><span class="check-ico">💰</span> Full loan disbursement — ₹{{ number_format($application->approved_amount, 0) }}</li>
        <li><span class="check-ico">📄</span> Instant sanction letter</li>
        <li><span class="check-ico">✍️</span> Digital e-signing (Aadhaar OTP)</li>
        <li><span class="check-ico">⚡</span> Disbursal in 24–48 hours</li>
        <li><span class="check-ico">🔄</span> Fee 100% refundable if not disbursed</li>
    </ul>

    {{-- Trust tiles --}}
    <div class="trust-tiles">
        <div class="trust-tile">
            <span class="t-icon">✅</span>
            <span class="t-label">Refundable</span>
        </div>
        <div class="trust-tile">
            <span class="t-icon">🔐</span>
            <span class="t-label">256-bit SSL</span>
        </div>
        <div class="trust-tile">
            <span class="t-icon">🏦</span>
            <span class="t-label">RBI Compliant</span>
        </div>
        <div class="trust-tile">
            <span class="t-icon">⚡</span>
            <span class="t-label">Instant</span>
        </div>
    </div>

    {{-- Price --}}
    <div class="price-row">
        <div>
            <div class="price-label">One-Time Processing Fee</div>
            <span>Fully refundable · No hidden charges</span>
        </div>
        <div class="price-value">₹299</div>
    </div>

    {{-- Razorpay payment button --}}
    <button type="button" class="btn-pay" id="payBtn">
        <span id="payText">Pay ₹299 &amp; Unlock ₹{{ number_format($application->approved_amount, 0) }} 🚀</span>
        <span id="payLoader" style="display:none;">Preparing Secure Payment…</span>
    </button>

    <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px; margin-top:16px;">
        <div style="display:flex; align-items:center; gap:12px; opacity:0.6; filter: grayscale(0.5);">
            <img src="https://upload.wikimedia.org/wikipedia/commons/e/e1/UPI-Logo.png" alt="UPI" height="15">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c7/Google_Pay_Logo.svg/1024px-Google_Pay_Logo.svg.png" alt="GPay" height="15">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/71/PhonePe_Logo.svg/1024px-PhonePe_Logo.svg.png" alt="PhonePe" height="15">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/24/Paytm_Logo_%28standalone%29.svg/1024px-Paytm_Logo_%28standalone%29.svg.png" alt="Paytm" height="10">
        </div>
        <p style="text-align:center; font-size:11.5px; color:#b45309; margin:0; font-weight:500;">
            <i class="bi bi-lock-fill"></i> Secure UPI & Card Payments by Razorpay
        </p>
    </div>
</div>

{{-- ── Info strip ──────────────────────────────────────────────────────────── --}}
<div style="padding: 18px 20px 32px;">
    <div class="alert alert-info" style="font-size:12.5px;">
        <i class="bi bi-info-circle-fill" style="flex-shrink:0;"></i>
        This offer is based on your profile submitted today. Approval is non-binding and subject to document verification.
    </div>
</div>

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
// ── 1. Confetti burst ────────────────────────────────────────────────────────
(function spawnConfetti() {
    const colors = ['#ffd54f','#69f0ae','#ef9a9a','#80deea','#ce93d8','#fff'];
    const zone   = document.getElementById('confettiZone');
    for (let i = 0; i < 28; i++) {
        const d  = document.createElement('div');
        d.className = 'confetti-dot';
        d.style.cssText = `
            left:${Math.random()*100}%;
            top:${Math.random()*60}%;
            background:${colors[Math.floor(Math.random()*colors.length)]};
            animation-delay:${(Math.random()*0.9).toFixed(2)}s;
            animation-duration:${(1.2+Math.random()*1).toFixed(2)}s;
            width:${6+Math.floor(Math.random()*5)}px;
            height:${6+Math.floor(Math.random()*5)}px;
        `;
        zone.appendChild(d);
    }
})();

// ── 2. Countdown timer ───────────────────────────────────────────────────────
(function countdown() {
    let secs = 14 * 60 + 59;  // 14:59
    const el = document.getElementById('countdown');
    if (!el) return;
    const tick = () => {
        if (secs <= 0) { el.textContent = '00:00'; return; }
        const m = String(Math.floor(secs / 60)).padStart(2,'0');
        const s = String(secs % 60).padStart(2,'0');
        el.textContent = `${m}:${s}`;
        secs--;
        setTimeout(tick, 1000);
    };
    tick();
})();

// ── 3. Razorpay payment ──────────────────────────────────────────────────────
document.getElementById('payBtn').addEventListener('click', function(e) {
    const btn    = document.getElementById('payBtn');
    const text   = document.getElementById('payText');
    const loader = document.getElementById('payLoader');
    
    btn.disabled  = true;
    text.style.display = 'none';
    loader.style.display = 'inline';

    fetch("{{ route('payment.initiate') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
            resetBtn();
            return;
        }

        const options = {
            "key": data.key,
            "amount": data.amount,
            "currency": "INR",
            "name": "First Smart Loan",
            "description": "Loan Processing Fee",
            "order_id": data.order_id,
            "handler": function (response){
                fetch("{{ route('payment.verify') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        razorpay_order_id: response.razorpay_order_id,
                        razorpay_payment_id: response.razorpay_payment_id,
                        razorpay_signature: response.razorpay_signature
                    })
                })
                .then(res => res.json())
                .then(verifyData => {
                    if (verifyData.status === 'success') {
                        window.location.href = "{{ route('payment.success') }}";
                    } else {
                        alert("Verification failed: " + verifyData.error);
                        resetBtn();
                    }
                });
            },
            "prefill": {
                "name": data.name,
                "email": data.email,
                "contact": data.mobile
            },
            "theme": {
                "color": "#3b5bdb"
            },
            "modal": {
                "ondismiss": function(){
                    resetBtn();
                }
            }
        };
        const rzp1 = new Razorpay(options);
        rzp1.open();
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Something went wrong. Please try again.");
        resetBtn();
    });

    function resetBtn() {
        btn.disabled = false;
        text.style.display = 'inline';
        loader.style.display = 'none';
    }
});
</script>
@endpush

@endsection
