@extends('layouts.app')
@section('title', 'Unlock Your Loan')
@section('content')

<div class="app-header">
    <a href="{{ route('application.pre_offer') }}" class="btn-back"><i class="bi bi-arrow-left"></i></a>
    <div style="font-weight:700; font-size:15px;">Unlock Loan</div>
</div>

<div class="app-content" style="flex:1; padding-top:8px;">

    {{-- ── Eligibility card ── --}}
    <div class="eligibility-card" style="position:relative; overflow:hidden;">
        <div style="position:absolute;top:-20px;right:-20px;font-size:80px;opacity:.08;">🎉</div>
        <div style="font-size:28px; margin-bottom:8px;">🎉</div>
        <div style="font-size:16px; font-weight:600; opacity:.85; margin-bottom:6px;">Congratulations, {{ $user->name ?? 'there' }}!</div>
        <div style="font-size:14px; opacity:.7; margin-bottom:20px;">You are eligible for</div>
        <div class="amount">₹{{ number_format($application->approved_amount ?? 50000) }}</div>
        <div style="font-size:13px; opacity:.7; margin-top:6px;">Approved Loan Amount</div>
    </div>

    {{-- ── Loan detail grid ── --}}
    <div class="loan-detail-grid">
        <div class="loan-detail-card">
            <div class="loan-detail-label">Monthly EMI</div>
            <div class="loan-detail-value" style="font-size:18px;">₹{{ number_format($application->emi_amount ?? 0) }}</div>
        </div>
        <div class="loan-detail-card">
            <div class="loan-detail-label">Tenure</div>
            <div class="loan-detail-value">{{ $application->tenure_months ?? 12 }} <span style="font-size:14px; color:var(--gray-600);">Mo</span></div>
        </div>
        <div class="loan-detail-card">
            <div class="loan-detail-label">Interest Rate</div>
            <div class="loan-detail-value">{{ $application->interest_rate ?? 18 }}<span style="font-size:14px; color:var(--gray-600);">% p.a.</span></div>
        </div>
        <div class="loan-detail-card">
            <div class="loan-detail-label">Credit Score</div>
            <div class="loan-detail-value" style="color: var(--green);">{{ $application->credit_score ?? 720 }}</div>
        </div>
    </div>

    {{-- ── Processing fee banner ── --}}
    <div style="border: 2px dashed var(--orange); border-radius: 18px; padding: 20px; margin-bottom: 20px; background: #FFFAEB;">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
            <div style="width:40px;height:40px;background: var(--orange); border-radius:12px; display:flex;align-items:center;justify-content:center; font-size:20px; flex-shrink:0;">🔒</div>
            <div>
                <div style="font-weight:800; font-size:16px;">One-Time Processing Fee</div>
                <div style="font-size:12px; color: var(--gray-600);">Required to proceed with your loan</div>
            </div>
        </div>
        <p style="font-size:13.5px; color: var(--gray-600); margin-bottom:16px; line-height:1.6;">
            To proceed and unlock your approved loan, pay a <strong>one-time refundable processing fee</strong> of <strong style="color: var(--orange); font-size:18px;">₹299</strong>. This is fully refundable if your loan is not disbursed.
        </p>
        <div style="display:flex; gap:10px;">
            <div style="background:#fff; border-radius:10px; padding:10px 12px; flex:1; text-align:center; font-size:12px; font-weight:600; color: var(--green);">✅<br>Refundable</div>
            <div style="background:#fff; border-radius:10px; padding:10px 12px; flex:1; text-align:center; font-size:12px; font-weight:600; color: var(--blue);">🔐<br>Secure</div>
            <div style="background:#fff; border-radius:10px; padding:10px 12px; flex:1; text-align:center; font-size:12px; font-weight:600; color: var(--orange);">⚡<br>Instant</div>
        </div>
    </div>

    {{-- Flash error (e.g. cancelled payment) --}}
    @if(session('error'))
    <div style="background:#FEF2F2; border:1px solid #FECACA; border-radius:12px; padding:12px 16px; font-size:13px; color:#B91C1C; margin-bottom:16px;">
        ⚠️ {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div style="background:#FEF2F2; border:1px solid #FECACA; border-radius:12px; padding:12px 16px; font-size:13px; color:#B91C1C; margin-bottom:16px;">
        ⚠️ {{ $errors->first('payment') }}
    </div>
    @endif

    {{-- ── Razorpay payment section ── --}}
    <div style="display:flex; justify-content:space-between; align-items:center; padding:16px; background: var(--gray-50); border-radius:12px; margin: 16px 0;">
        <span style="font-weight:600;">Total Payable</span>
        <span style="font-size:26px; font-weight:800; color: var(--blue);">₹299</span>
    </div>

    <button type="button" id="payBtn" class="btn-primary" style="background: var(--orange); font-size:16px; display:flex; align-items:center; justify-content:center; gap:10px;">
        <span id="payBtnText">Pay ₹299 Securely &amp; Unlock Loan 🚀</span>
        <span id="payBtnSpinner" style="display:none;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation:spin 0.8s linear infinite;">
                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
            </svg>
        </span>
    </button>

    <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px; margin-top:16px;">
        <div style="display:flex; align-items:center; gap:12px; opacity:0.6; filter: grayscale(0.5);">
            <img src="https://upload.wikimedia.org/wikipedia/commons/e/e1/UPI-Logo.png" alt="UPI" height="15">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c7/Google_Pay_Logo.svg/1024px-Google_Pay_Logo.svg.png" alt="GPay" height="15">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/71/PhonePe_Logo.svg/1024px-PhonePe_Logo.svg.png" alt="PhonePe" height="15">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/24/Paytm_Logo_%28standalone%29.svg/1024px-Paytm_Logo_%28standalone%29.svg.png" alt="Paytm" height="10">
        </div>
        <span style="font-size:11.5px; color: var(--gray-400);">
            <i class="bi bi-lock-fill"></i> Secure UPI & Card Payments by Razorpay
        </span>
    </div>
</div>

<style>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('payBtn').addEventListener('click', function(e) {
    const btn    = document.getElementById('payBtn');
    const text   = document.getElementById('payBtnText');
    const spinner = document.getElementById('payBtnSpinner');
    
    btn.disabled  = true;
    text.textContent = 'Preparing Payment…';
    spinner.style.display = 'inline-block';

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
            "name": "LoanUnlock",
            "description": "Loan Processing Fee",
            "order_id": data.order_id,
            "handler": function (response){
                // Verify payment on backend
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
        text.textContent = 'Pay ₹299 Securely & Unlock Loan 🚀';
        spinner.style.display = 'none';
    }
});
</script>
@endsection
