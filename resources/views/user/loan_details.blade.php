@extends('layouts.app')
@section('title', 'Loan Details')
@section('content')

<div class="progress-bar-top">
    <div class="progress-segment done"></div>
    <div class="progress-segment done"></div>
    <div class="progress-segment done"></div>
    <div class="progress-segment active"></div>
</div>

<div class="app-header">
    <a href="{{ route('application.permissions') }}" class="btn-back"><i class="bi bi-arrow-left"></i></a>
    <div>
        <div style="font-size:12px; color: var(--gray-400); font-weight:500;">Step 3 of 4</div>
        <div style="font-weight:700; font-size:15px;">Loan Details</div>
    </div>
</div>

<form action="{{ route('application.loan.save') }}" method="POST" style="display:flex; flex-direction:column; flex:1;">
@csrf
<div class="app-content">
    <h1 class="screen-title">Let's personalise your loan</h1>
    <p class="screen-subtitle">Your work details and preferences help us find the right loan for you.</p>

    @if($errors->any())
        <div class="alert alert-error"><i class="bi bi-exclamation-circle-fill"></i> Please fix the errors below.</div>
    @endif

    <div class="form-group">
        <label class="form-label">Employment Type *</label>
        <div class="radio-card-group">
            <label class="radio-card">
                <input type="radio" name="employment_type" value="salaried" {{ old('employment_type', $user->employment_type) == 'salaried' ? 'checked' : '' }}>
                <div class="radio-card-icon">💼</div>
                <div class="radio-card-label">Salaried</div>
            </label>
            <label class="radio-card">
                <input type="radio" name="employment_type" value="business" {{ old('employment_type', $user->employment_type) == 'business' ? 'checked' : '' }}>
                <div class="radio-card-icon">🏢</div>
                <div class="radio-card-label">Business / Others</div>
            </label>
        </div>
        @error('employment_type') <div class="invalid-feedback" style="display:block; margin-top:6px;">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Company / Business Name *</label>
        <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror"
               placeholder="Where do you work?" value="{{ old('company_name', $user->company_name) }}">
        @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Monthly Income (₹) *</label>
        <input type="number" name="monthly_income" class="form-control @error('monthly_income') is-invalid @enderror"
               placeholder="e.g. 35000" min="5000" inputmode="numeric"
               value="{{ old('monthly_income', $user->monthly_income) }}">
        @error('monthly_income') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Loan Purpose *</label>
        <select name="loan_purpose" class="form-control @error('loan_purpose') is-invalid @enderror">
            <option value="">Select purpose</option>
            @foreach(['Home Renovation', 'Medical Emergency', 'Education', 'Wedding', 'Travel', 'Business Expansion', 'Debt Consolidation', 'Vehicle Purchase', 'Electronics/Appliances', 'Other'] as $purpose)
                <option value="{{ $purpose }}" {{ old('loan_purpose') == $purpose ? 'selected' : '' }}>{{ $purpose }}</option>
            @endforeach
        </select>
        @error('loan_purpose') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Loan Amount Required (₹) *</label>
        <input type="number" name="requested_amount" class="form-control @error('requested_amount') is-invalid @enderror"
               placeholder="e.g. 50000" min="5000" max="500000" inputmode="numeric"
               value="{{ old('requested_amount', $application->requested_amount ?? '') }}" id="loanAmount">
        <div style="display:flex; justify-content:space-between; margin-top: 6px; font-size: 12px; color: var(--gray-400);">
            <span>Min: ₹5,000</span><span>Max: ₹5,00,000</span>
        </div>
        @error('requested_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Preferred Tenure *</label>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;">
            @foreach([3, 6, 12, 18, 24, 36] as $t)
            <label style="border: 1.5px solid var(--gray-200); border-radius: 10px; padding: 12px; text-align:center; cursor:pointer; font-weight:600; font-size:14px; transition: all .2s;"
                   class="tenure-card" data-tenure="{{ $t }}">
                <input type="radio" name="tenure_months" value="{{ $t }}" style="display:none;" {{ old('tenure_months', $application->tenure_months ?? '') == $t ? 'checked' : '' }}>
                {{ $t }} Mo
            </label>
            @endforeach
        </div>
        @error('tenure_months') <div class="invalid-feedback" style="display:block;">{{ $message }}</div> @enderror
    </div>

    <!-- Live EMI Preview -->
    <div id="emiPreview" style="background: linear-gradient(135deg, var(--blue) 0%, #5B7FE8 100%); border-radius: 16px; padding: 20px; color:#fff; margin-bottom: 16px; display:none;">
        <div style="font-size:13px; opacity:.8; margin-bottom:4px;">Estimated Monthly EMI</div>
        <div style="font-size:32px; font-weight:800;" id="emiAmount">₹0</div>
        <div style="font-size:12px; opacity:.7; margin-top:4px;">at 18% p.a. interest rate</div>
    </div>
</div>

<div class="sticky-bottom">
    <button type="submit" class="btn-primary">SUBMIT &amp; CHECK YOUR ELIGIBILITY <i class="bi bi-arrow-right"></i></button>
</div>
</form>

@push('scripts')
<script>
// Tenure card UI
document.querySelectorAll('.tenure-card').forEach(card => {
    const radio = card.querySelector('input');
    if (radio.checked) {
        card.style.borderColor = 'var(--blue)';
        card.style.background = 'var(--blue-light)';
        card.style.color = 'var(--blue)';
    }
    card.addEventListener('click', function() {
        document.querySelectorAll('.tenure-card').forEach(c => {
            c.style.borderColor = 'var(--gray-200)';
            c.style.background = '#fff';
            c.style.color = 'var(--gray-900)';
        });
        radio.checked = true;
        this.style.borderColor = 'var(--blue)';
        this.style.background = 'var(--blue-light)';
        this.style.color = 'var(--blue)';
        calcEMI();
    });
});

function calcEMI() {
    const amount = parseFloat(document.getElementById('loanAmount').value) || 0;
    const tenureRadio = document.querySelector('input[name="tenure_months"]:checked');
    if (!amount || !tenureRadio) { document.getElementById('emiPreview').style.display = 'none'; return; }
    const n = parseInt(tenureRadio.value);
    const r = 18 / 100 / 12;
    const emi = amount * r * Math.pow(1+r,n) / (Math.pow(1+r,n)-1);
    document.getElementById('emiAmount').textContent = '₹' + Math.round(emi).toLocaleString('en-IN');
    document.getElementById('emiPreview').style.display = 'block';
}

document.getElementById('loanAmount').addEventListener('input', calcEMI);
calcEMI();
</script>
@endpush
@endsection
