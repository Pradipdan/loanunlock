@extends('layouts.app')
@section('title', 'Verifying Documents')

@push('styles')
<style>
    .checking-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex: 1;
        padding: 40px 24px;
        text-align: center;
    }

    /* AI Pulse Animation */
    .pulse-wrapper {
        position: relative;
        width: 140px;
        height: 140px;
        margin-bottom: 40px;
    }
    .pulse-circle {
        position: absolute;
        width: 100%;
        height: 100%;
        background: var(--blue-light);
        border-radius: 50%;
        opacity: 0;
        animation: pulseLoop 2.5s infinite;
    }
    .pulse-circle:nth-child(2) { animation-delay: .8s; }
    .pulse-circle:nth-child(3) { animation-delay: 1.6s; }
    
    @keyframes pulseLoop {
        0% { transform: scale(0.5); opacity: 0.8; }
        100% { transform: scale(1.5); opacity: 0; }
    }

    .pulse-core {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        width: 80px; height: 80px;
        background: var(--blue);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: #fff;
        font-size: 32px;
        box-shadow: 0 8px 32px rgba(59,91,219,0.3);
        z-index: 2;
    }

    .status-text {
        font-size: 20px;
        font-weight: 800;
        color: var(--gray-900);
        margin-bottom: 12px;
    }
    .status-sub {
        font-size: 14px;
        color: var(--gray-600);
        margin-bottom: 32px;
    }

    .check-list {
        width: 100%;
        max-width: 280px;
        text-align: left;
        margin: 0 auto;
    }
    .check-item {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
        font-weight: 600;
        font-size: 14px;
        color: var(--gray-400);
        transition: color .3s;
    }
    .check-item.active { color: var(--gray-900); }
    .check-item.done { color: var(--green); }
    
    .check-item .dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: var(--gray-200);
    }
    .check-item.active .dot { background: var(--blue); transform: scale(1.3); animation: blink 1s infinite; }
    .check-item.done .dot { background: var(--green); }

    @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

    .bureau-logos {
        margin-top: 50px;
        opacity: 0.5;
        display: flex;
        gap: 20px;
        filter: grayscale(1);
    }
</style>
@endpush

@section('content')
<div class="checking-container">
    <div class="pulse-wrapper">
        <div class="pulse-circle"></div>
        <div class="pulse-circle"></div>
        <div class="pulse-circle"></div>
        <div class="pulse-core">
            <i class="bi bi-shield-check"></i>
        </div>
    </div>

    <div class="status-text" id="mainStatus">Verifying details...</div>
    <div class="status-sub">Please do not close the app or press back.</div>

    <div class="check-list">
        <div class="check-item active" id="check1">
            <div class="dot"></div>
            Connecting to Credit Bureau
        </div>
        <div class="check-item" id="check2">
            <div class="dot"></div>
            Fetching CIBIL Score
        </div>
        <div class="check-item" id="check3">
            <div class="dot"></div>
            Analyzing Loan Eligibility
        </div>
        <div class="check-item" id="check4">
            <div class="dot"></div>
            Generating Best Offers
        </div>
    </div>

    <div class="bureau-logos">
        <span style="font-size: 10px; font-weight: 700; letter-spacing: 1px; color: var(--gray-400);">PARTNERED WITH</span>
        <!-- Bureau names as text for simplicity in this demo -->
        <span style="font-weight: 800; font-size: 12px;">CIBIL</span>
        <span style="font-weight: 800; font-size: 12px;">Experian</span>
        <span style="font-weight: 800; font-size: 12px;">Equifax</span>
    </div>
</div>

@push('scripts')
<script>
    const steps = [
        { id: 'check1', time: 18000, status: 'Connected' },
        { id: 'check2', time: 32000, status: 'Score: 752' },
        { id: 'check3', time: 25000, status: 'Eligible: ₹5,00,000' },
        { id: 'check4', time: 20000, status: 'Offer Ready!' }
    ];

    let currentStep = 0;

    function processStep() {
        if (currentStep < steps.length) {
            const step = steps[currentStep];
            const el = document.getElementById(step.id);
            
            setTimeout(() => {
                el.classList.remove('active');
                el.classList.add('done');
                el.innerHTML = `<i class="bi bi-check-circle-fill" style="color: var(--green); font-size: 18px;"></i> ${el.innerText}`;
                
                currentStep++;
                if (currentStep < steps.length) {
                    document.getElementById(steps[currentStep].id).classList.add('active');
                    processStep();
                } else {
                    document.getElementById('mainStatus').innerHTML = 'Congratulations! 🎉';
                    document.getElementById('mainStatus').style.color = 'var(--green)';
                    setTimeout(() => {
                        window.location.href = "{{ route('application.pre_offer') }}";
                    }, 800);
                }
            }, step.time);
        }
    }

    window.onload = processStep;
</script>
@endpush
@endsection
