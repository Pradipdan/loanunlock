@extends('layouts.app')
@section('title', 'Personal Details')
@section('content')

<div class="progress-bar-top">
    <div class="progress-segment done"></div>
    <div class="progress-segment active"></div>
    <div class="progress-segment"></div>
    <div class="progress-segment"></div>
</div>

<div class="app-header">
    <a href="{{ route('otp.mobile') }}" class="btn-back"><i class="bi bi-arrow-left"></i></a>
    <div>
        <div style="font-size:12px; color: var(--gray-400); font-weight:500;">Step 1 of 4</div>
        <div style="font-weight:700; font-size:15px;">Personal Details</div>
    </div>
</div>

<form action="{{ route('application.personal.save') }}" method="POST" style="display:flex; flex-direction:column; flex:1;">
@csrf
<div class="app-content">
    <h1 class="screen-title">Tell us about you</h1>
    <p class="screen-subtitle">Just a few basic details to unlock loan offers for you.</p>

    @if($errors->any())
        <div class="alert alert-error"><i class="bi bi-exclamation-circle-fill"></i> Please fix the errors below.</div>
    @endif

    <div class="form-group">
        <label class="form-label">Full Name (As per PAN) *</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
               placeholder="e.g. Pradip Kumar Patel" value="{{ old('name', $user->name) }}" autocomplete="name">
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label class="form-label">PAN Number *</label>
        <input type="text" name="pan_number" class="form-control @error('pan_number') is-invalid @enderror"
               placeholder="e.g. ABCDE1234F" value="{{ old('pan_number', $user->pan_number) }}"
               maxlength="10" style="text-transform:uppercase;">
        @error('pan_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Date of Birth *</label>
        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror"
               value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
               max="{{ date('Y-m-d', strtotime('-18 years')) }}">
        @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
               placeholder="your@email.com" value="{{ old('email', $user->email) }}">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label class="form-label">State *</label>
        <select name="state" class="form-control @error('state') is-invalid @enderror">
            <option value="">Select your state</option>
            @foreach(['Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal','Delhi','Jammu and Kashmir','Ladakh'] as $state)
                <option value="{{ $state }}" {{ old('state', $user->state) == $state ? 'selected' : '' }}>{{ $state }}</option>
            @endforeach
        </select>
        @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
        <div class="form-group">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control" placeholder="Ahmedabad" value="{{ old('city', $user->city) }}">
        </div>
        <div class="form-group">
            <label class="form-label">Pincode</label>
            <input type="text" name="pincode" class="form-control" placeholder="380001" maxlength="6" inputmode="numeric" value="{{ old('pincode', $user->pincode) }}">
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Preferred Language *</label>
        <select name="preferred_language" class="form-control">
            @foreach(['ENGLISH', 'HINDI', 'GUJARATI', 'MARATHI', 'TAMIL', 'TELUGU', 'KANNADA', 'BENGALI'] as $lang)
                <option value="{{ $lang }}" {{ old('preferred_language', $user->preferred_language) == $lang ? 'selected' : '' }}>{{ ucfirst(strtolower($lang)) }}</option>
            @endforeach
        </select>
    </div>

    <div class="alert alert-warning">
        <i class="bi bi-info-circle-fill"></i>
        Your selected language will be used to share loan-related documents.
    </div>

    <div class="consent-wrap">
        <input type="checkbox" name="consent" id="consent" required>
        <label for="consent" class="consent-text">
            I authorize Smart Loan and its partners to fetch my credit information from credit bureaus (CIBIL/Experian etc.) and verify my details for this application. I agree to the <a href="#">Terms of Use</a>.
        </label>
    </div>
</div>

<div class="sticky-bottom">
    <button type="submit" class="btn-primary">Continue <i class="bi bi-arrow-right"></i></button>
</div>
</form>

@push('scripts')
<script>
    document.querySelector('input[name="pan_number"]').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
</script>
@endpush
@endsection
