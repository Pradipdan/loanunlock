@extends('layouts.admin')
@section('title','User Profile')
@section('page-title','User Profile')
@section('content')

<div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
    <div style="font-size:18px;font-weight:800;">{{ $user->name ?? 'Unknown User' }}</div>
    @if($user->is_blocked)
        <span class="badge badge-danger">Blocked</span>
    @else
        <span class="badge badge-success">Active</span>
    @endif
    <div style="margin-left:auto;display:flex;gap:8px;">
        @if($user->is_blocked)
            <form action="{{ route('admin.users.unblock',$user->id) }}" method="POST">@csrf
                <button type="submit" class="btn btn-success btn-sm">Unblock User</button></form>
        @else
            <form action="{{ route('admin.users.block',$user->id) }}" method="POST" onsubmit="return confirm('Block this user?')">@csrf
                <button type="submit" class="btn btn-danger btn-sm">Block User</button></form>
        @endif
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    <div class="card">
        <div class="card-header"><div class="card-title">Personal Information</div></div>
        <div class="card-body">
            @foreach([
                'Mobile' => $user->mobile,
                'Email' => $user->email ?? '—',
                'PAN' => $user->pan_number ?? '—',
                'Aadhar' => $user->aadhar_number ? substr($user->aadhar_number,0,4).'****'.substr($user->aadhar_number,-4) : '—',
                'Date of Birth' => $user->date_of_birth?->format('d M Y') ?? '—',
                'State' => $user->state ?? '—',
                'City' => $user->city ?? '—',
                'Pincode' => $user->pincode ?? '—',
                'Address' => $user->address ?? '—',
                'Language' => $user->preferred_language ?? '—',
            ] as $label => $val)
            <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--gray-100);">
                <span style="font-size:13px;color:var(--gray-600);">{{ $label }}</span>
                <span style="font-size:13.5px;font-weight:600;text-align:right;max-width:60%;">{{ $val }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <div class="card-header"><div class="card-title">Employment & Financial</div></div>
        <div class="card-body">
            @foreach([
                'Employment Type' => ucfirst($user->employment_type ?? '—'),
                'Company/Business' => $user->company_name ?? '—',
                'Monthly Income' => $user->monthly_income ? '₹'.number_format($user->monthly_income) : '—',
                'Bank Account' => $user->bank_account ? '****'.substr($user->bank_account,-4) : '—',
                'Bank IFSC' => $user->bank_ifsc ?? '—',
                'Bank Name' => $user->bank_name ?? '—',
                'Verified' => $user->is_verified ? '✅ Yes' : '❌ No',
                'Permissions' => $user->permissions_granted ? '✅ Granted' : '❌ Not granted',
                'Joined On' => $user->created_at->format('d M Y, h:i A'),
            ] as $label => $val)
            <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--gray-100);">
                <span style="font-size:13px;color:var(--gray-600);">{{ $label }}</span>
                <span style="font-size:13.5px;font-weight:600;">{{ $val }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Loan Applications --}}
@if($user->loanApplications->count())
<div class="card" style="margin-top:20px;">
    <div class="card-header"><div class="card-title">Loan Applications ({{ $user->loanApplications->count() }})</div></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>App ID</th><th>Amount</th><th>EMI</th><th>Status</th><th>Applied</th><th></th></tr></thead>
            <tbody>
                @foreach($user->loanApplications->sortByDesc('created_at') as $app)
                <tr>
                    <td style="font-family:monospace;font-size:12px;color:var(--blue);font-weight:700;">{{ $app->application_id }}</td>
                    <td>₹{{ number_format($app->approved_amount ?? $app->requested_amount ?? 0) }}</td>
                    <td>{{ $app->emi_amount ? '₹'.number_format($app->emi_amount) : '—' }}</td>
                    <td>
                        @php $bc=match($app->status){'approved','disbursed'=>'badge-success','rejected'=>'badge-danger','under_review'=>'badge-warning',default=>'badge-secondary'}; @endphp
                        <span class="badge {{ $bc }}">{{ ucwords(str_replace('_',' ',$app->status)) }}</span>
                    </td>
                    <td style="font-size:12.5px;color:var(--gray-400);">{{ $app->created_at->format('d M Y') }}</td>
                    <td><a href="{{ route('admin.loans.show',$app->id) }}" class="btn btn-outline btn-sm">Review</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
