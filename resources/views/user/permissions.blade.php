@extends('layouts.app')
@section('title', 'Permissions')
@section('content')

<div class="progress-bar-top">
    <div class="progress-segment done"></div>
    <div class="progress-segment done"></div>
    <div class="progress-segment active"></div>
    <div class="progress-segment"></div>
</div>

<div class="app-header">
    <a href="{{ route('application.personal') }}" class="btn-back"><i class="bi bi-arrow-left"></i></a>
    <div>
        <div style="font-size:12px; color: var(--gray-400); font-weight:500;">Step 2 of 4</div>
        <div style="font-weight:700; font-size:15px;">Permissions</div>
    </div>
</div>

<div class="app-content" style="flex:1;">
    <h1 class="screen-title">Allow the Permissions</h1>
    <p class="screen-subtitle">We need these permissions to verify your identity and provide the best loan offers.</p>

    <div style="background: var(--gray-50); border-radius: 18px; padding: 8px 16px; margin-bottom: 24px;">
        <div class="permission-list">
            <div class="permission-item">
                <div class="perm-icon"><i class="bi bi-camera-fill"></i></div>
                <div>
                    <div class="perm-title">Camera</div>
                    <div class="perm-desc">Used only once to capture your photo and documents for KYC.</div>
                </div>
            </div>
            <div class="permission-item">
                <div class="perm-icon" style="background: #FFF7E6; color: var(--orange);">
                    <i class="bi bi-geo-alt-fill"></i>
                </div>
                <div>
                    <div class="perm-title">Location <span style="font-size:11px; background: var(--blue-light); color: var(--blue); padding: 2px 6px; border-radius: 99px; font-weight:600;">One-Time Access</span></div>
                    <div class="perm-desc">To verify your service area and validate address details.</div>
                </div>
            </div>
            <div class="permission-item">
                <div class="perm-icon" style="background: #ECFDF3; color: var(--green);">
                    <i class="bi bi-phone-fill"></i>
                </div>
                <div>
                    <div class="perm-title">Phone Number &amp; Device Info</div>
                    <div class="perm-desc">To verify your identity, prevent fraud, and send OTPs securely.</div>
                </div>
            </div>
            <div class="permission-item">
                <div class="perm-icon" style="background: #FEF3F2; color: var(--red);">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>
                <div>
                    <div class="perm-title">Storage</div>
                    <div class="perm-desc">To save loan documents and statements for your reference.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="bi bi-info-circle-fill" style="flex-shrink:0;"></i>
        <div>Your permissions are used only for loan processing purposes and are never shared for marketing. You can revoke anytime from app settings.</div>
    </div>

    <div class="secure-badge">
        <i class="bi bi-shield-fill-check"></i> Your data is 100% encrypted and secure with us.
    </div>
</div>

<div class="sticky-bottom">
    <form action="{{ route('application.permissions.save') }}" method="POST">
        @csrf
        <button type="submit" class="btn-primary">PROCEED <i class="bi bi-arrow-right"></i></button>
    </form>
</div>
@endsection
