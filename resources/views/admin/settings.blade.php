@extends('layouts.admin')
@section('title','Settings')
@section('page-title','System Settings')
@section('content')
<div style="max-width:640px;">
    <div class="card">
        <div class="card-header"><div class="card-title">⚙️ General Settings</div></div>
        <div class="card-body">
            <form action="{{ route('admin.settings.save') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Processing Fee (₹)</label>
                    <input type="number" name="processing_fee" class="form-control" value="299" min="0">
                    <div style="font-size:12px;color:var(--gray-400);margin-top:5px;">Amount charged to unlock the loan application.</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Default Interest Rate (%)</label>
                    <input type="number" name="default_interest_rate" class="form-control" value="18" min="1" max="36" step="0.1">
                </div>
                <div class="form-group">
                    <label class="form-label">Max Loan Amount (₹)</label>
                    <input type="number" name="max_loan_amount" class="form-control" value="500000">
                </div>
                <div class="form-group">
                    <label class="form-label">Min Monthly Income for Eligibility (₹)</label>
                    <input type="number" name="min_income" class="form-control" value="15000">
                </div>
                <div class="form-group">
                    <label class="form-label">Support Email</label>
                    <input type="email" name="support_email" class="form-control" value="support@loanunlock.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Support Phone</label>
                    <input type="text" name="support_phone" class="form-control" value="1800-986-3452">
                </div>
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </form>
        </div>
    </div>

    <div class="card" style="margin-top:20px;">
        <div class="card-header"><div class="card-title">🔐 Change Admin Password</div></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Current Password</label>
                <input type="password" class="form-control" placeholder="••••••••">
            </div>
            <div class="form-group">
                <label class="form-label">New Password</label>
                <input type="password" class="form-control" placeholder="••••••••">
            </div>
            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" placeholder="••••••••">
            </div>
            <button class="btn btn-primary">Update Password</button>
        </div>
    </div>
</div>
@endsection
