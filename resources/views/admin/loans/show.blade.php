@extends('layouts.admin')
@section('title','Review Application')
@section('page-title','Application Review')
@section('content')

@php
    $app  = $application;
    $user = $application->user;
    $bc   = match($app->status){'approved','disbursed'=>'badge-success','rejected'=>'badge-danger','under_review'=>'badge-warning','payment_done'=>'badge-info',default=>'badge-secondary'};
@endphp

<div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;flex-wrap:wrap;">
    <a href="{{ route('admin.loans.index') }}" class="btn btn-outline btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
    <span style="font-family:monospace;font-weight:700;color:var(--blue);font-size:15px;">{{ $app->application_id }}</span>
    <span class="badge {{ $bc }}">{{ ucwords(str_replace('_',' ',$app->status)) }}</span>
    <span style="font-size:13px;color:var(--gray-400);margin-left:auto;">Applied {{ $app->created_at->format('d M Y, h:i A') }}</span>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">

    {{-- LEFT COLUMN --}}
    <div style="display:flex;flex-direction:column;gap:20px;">

        {{-- Borrower Info --}}
        <div class="card">
            <div class="card-header"><div class="card-title">👤 Borrower Information</div>
                <a href="{{ route('admin.users.show',$user->id) }}" class="btn btn-outline btn-sm">Full Profile</a>
            </div>
            <div class="card-body">
                <div class="form-row cols-3">
                    <div><div style="font-size:12px;color:var(--gray-400);margin-bottom:4px;">Full Name</div><div style="font-weight:700;">{{ $user->name ?? '—' }}</div></div>
                    <div><div style="font-size:12px;color:var(--gray-400);margin-bottom:4px;">Mobile</div><div style="font-weight:700;">{{ $user->mobile }}</div></div>
                    <div><div style="font-size:12px;color:var(--gray-400);margin-bottom:4px;">PAN</div><div style="font-weight:700;font-family:monospace;">{{ $user->pan_number ?? '—' }}</div></div>
                    <div><div style="font-size:12px;color:var(--gray-400);margin-bottom:4px;">Date of Birth</div><div style="font-weight:600;">{{ $user->date_of_birth?->format('d M Y') ?? '—' }}</div></div>
                    <div><div style="font-size:12px;color:var(--gray-400);margin-bottom:4px;">State</div><div style="font-weight:600;">{{ $user->state ?? '—' }}</div></div>
                    <div><div style="font-size:12px;color:var(--gray-400);margin-bottom:4px;">Email</div><div style="font-weight:600;">{{ $user->email ?? '—' }}</div></div>
                    <div><div style="font-size:12px;color:var(--gray-400);margin-bottom:4px;">Employment</div><div style="font-weight:600;">{{ ucfirst($user->employment_type ?? '—') }}</div></div>
                    <div><div style="font-size:12px;color:var(--gray-400);margin-bottom:4px;">Monthly Income</div><div style="font-weight:700;color:var(--green);">₹{{ number_format($user->monthly_income ?? 0) }}</div></div>
                    <div><div style="font-size:12px;color:var(--gray-400);margin-bottom:4px;">Company</div><div style="font-weight:600;">{{ $user->company_name ?? '—' }}</div></div>
                </div>
            </div>
        </div>

        {{-- Loan Details --}}
        <div class="card">
            <div class="card-header"><div class="card-title">💰 Loan Details</div></div>
            <div class="card-body">
                <div class="form-row cols-3">
                    <div><div style="font-size:12px;color:var(--gray-400);margin-bottom:4px;">Requested Amount</div><div style="font-weight:700;font-size:18px;color:var(--blue);">₹{{ number_format($app->requested_amount ?? 0) }}</div></div>
                    <div><div style="font-size:12px;color:var(--gray-400);margin-bottom:4px;">Approved Amount</div><div style="font-weight:700;font-size:18px;color:var(--green);">{{ $app->approved_amount ? '₹'.number_format($app->approved_amount) : '—' }}</div></div>
                    <div><div style="font-size:12px;color:var(--gray-400);margin-bottom:4px;">Monthly EMI</div><div style="font-weight:700;font-size:18px;">{{ $app->emi_amount ? '₹'.number_format($app->emi_amount) : '—' }}</div></div>
                    <div><div style="font-size:12px;color:var(--gray-400);margin-bottom:4px;">Tenure</div><div style="font-weight:600;">{{ $app->tenure_months ?? '—' }} months</div></div>
                    <div><div style="font-size:12px;color:var(--gray-400);margin-bottom:4px;">Interest Rate</div><div style="font-weight:600;">{{ $app->interest_rate ?? '—' }}% p.a.</div></div>
                    <div><div style="font-size:12px;color:var(--gray-400);margin-bottom:4px;">Credit Score</div><div style="font-weight:700;color:{{ ($app->credit_score??0)>=700?'var(--green)':'var(--orange)' }};">{{ $app->credit_score ?? '—' }}</div></div>
                    <div><div style="font-size:12px;color:var(--gray-400);margin-bottom:4px;">Loan Purpose</div><div style="font-weight:600;">{{ $app->loan_purpose ?? '—' }}</div></div>
                    <div><div style="font-size:12px;color:var(--gray-400);margin-bottom:4px;">Processing Fee</div><div style="font-weight:600;">₹{{ $app->processing_fee }}</div></div>
                    <div><div style="font-size:12px;color:var(--gray-400);margin-bottom:4px;">Eligible</div><div style="font-weight:600;">{{ $app->is_eligible ? '✅ Yes' : '❌ No' }}</div></div>
                </div>
            </div>
        </div>

        {{-- Payment Info --}}
        @if($application->payments->count())
        <div class="card">
            <div class="card-header"><div class="card-title">💳 Payment History</div></div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>TXN ID</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        @foreach($application->payments as $payment)
                        <tr>
                            <td style="font-family:monospace;font-size:12px;">{{ $payment->transaction_id }}</td>
                            <td>₹{{ $payment->amount }}</td>
                            <td>{{ strtoupper($payment->method ?? '—') }}</td>
                            <td><span class="badge {{ $payment->status==='success'?'badge-success':'badge-warning' }}">{{ ucfirst($payment->status) }}</span></td>
                            <td style="font-size:12.5px;color:var(--gray-400);">{{ $payment->paid_at?->format('d M Y, h:i A') ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Documents --}}
        @if($application->documents->count())
        <div class="card">
            <div class="card-header">
                <div class="card-title">📁 Documents</div>
                <a href="{{ route('admin.loans.documents',$app->id) }}" class="btn btn-outline btn-sm">View All</a>
            </div>
            <div class="card-body" style="display:flex;flex-wrap:wrap;gap:10px;">
                @foreach($application->documents as $doc)
                <a href="{{ $doc->url }}" target="_blank" style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:var(--gray-50);border-radius:10px;text-decoration:none;color:var(--gray-900);border:1px solid var(--gray-200);">
                    <span style="font-size:18px;">📄</span>
                    <div>
                        <div style="font-size:12px;font-weight:700;">{{ ucwords(str_replace('_',' ',$doc->type)) }}</div>
                        <div style="font-size:11px;color:var(--gray-400);">
                            @php $vs=['verified'=>'✅ Verified','rejected'=>'❌ Rejected','pending'=>'⏳ Pending']; @endphp
                            {{ $vs[$doc->verification_status] ?? $doc->verification_status }}
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Admin Notes History --}}
        @if($application->notes->count())
        <div class="card">
            <div class="card-header"><div class="card-title">📝 Activity Log</div></div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
                @foreach($application->notes->sortByDesc('created_at') as $note)
                <div style="padding:14px;background:var(--gray-50);border-radius:10px;border-left:3px solid {{ match($note->type){'approval'=>'var(--green)','rejection'=>'var(--red)','disbursement'=>'var(--blue)',default=>'var(--gray-200)'} }};">
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                        <span style="font-weight:700;font-size:13px;">{{ $note->admin->name ?? 'Admin' }}</span>
                        <span style="font-size:11.5px;color:var(--gray-400);">{{ $note->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                    <div style="font-size:13.5px;color:var(--gray-600);">{{ $note->note }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- RIGHT COLUMN – Actions --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Approve Action --}}
        @if(in_array($app->status,['under_review','payment_done','eligibility_checked']))
        <div class="card">
            <div class="card-header" style="background:#ECFDF3;"><div class="card-title" style="color:var(--green);">✅ Approve Loan</div></div>
            <div class="card-body">
                <form action="{{ route('admin.loans.approve',$app->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Approved Amount (₹) *</label>
                        <input type="number" name="approved_amount" class="form-control" value="{{ $app->requested_amount }}" min="1000" required>
                    </div>
                    <div class="form-row cols-2">
                        <div class="form-group">
                            <label class="form-label">Tenure (months) *</label>
                            <select name="tenure_months" class="form-control" required>
                                @foreach([3,6,12,18,24,36] as $t)
                                <option value="{{ $t }}" {{ $app->tenure_months==$t?'selected':'' }}>{{ $t }} Months</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Interest Rate (%) *</label>
                            <input type="number" name="interest_rate" class="form-control" value="{{ $app->interest_rate ?? 18 }}" min="1" max="36" step="0.1" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Note (optional)</label>
                        <textarea name="note" class="form-control" placeholder="Any approval remarks..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-success" style="width:100%;" onclick="return confirm('Approve this loan?')">
                        ✅ Approve Loan
                    </button>
                </form>
            </div>
        </div>

        {{-- Reject Action --}}
        <div class="card">
            <div class="card-header" style="background:#FEF3F2;"><div class="card-title" style="color:var(--red);">❌ Reject Application</div></div>
            <div class="card-body">
                <form action="{{ route('admin.loans.reject',$app->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Rejection Reason *</label>
                        <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Explain clearly why this is being rejected..." required minlength="10"></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger" style="width:100%;" onclick="return confirm('Reject this application? This cannot be undone.')">
                        ❌ Reject Application
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Disburse --}}
        @if($app->status === 'approved')
        <div class="card">
            <div class="card-header" style="background:var(--blue-light);"><div class="card-title" style="color:var(--blue);">💰 Mark as Disbursed</div></div>
            <div class="card-body">
                <p style="font-size:13px;color:var(--gray-600);margin-bottom:14px;">Confirm that ₹{{ number_format($app->approved_amount) }} has been transferred to the borrower's account.</p>
                <form action="{{ route('admin.loans.disburse',$app->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Note</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="e.g. Transferred via NEFT to SBI account..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;" onclick="return confirm('Mark as disbursed?')">
                        💰 Mark as Disbursed
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Add Note --}}
        <div class="card">
            <div class="card-header"><div class="card-title">📝 Add Note</div></div>
            <div class="card-body">
                <form action="{{ route('admin.loans.note',$app->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <select name="type" class="form-control" style="margin-bottom:10px;">
                            <option value="general">General Note</option>
                            <option value="query">Query / Follow-up</option>
                        </select>
                        <textarea name="note" class="form-control" rows="3" placeholder="Add a note about this application..." required minlength="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-outline" style="width:100%;">Add Note</button>
                </form>
            </div>
        </div>

        {{-- Rejection Info --}}
        @if($app->status === 'rejected' && $app->rejection_reason)
        <div class="card" style="border:1.5px solid var(--red);">
            <div class="card-header"><div class="card-title" style="color:var(--red);">Rejection Reason</div></div>
            <div class="card-body" style="font-size:13.5px;color:var(--gray-600);">{{ $app->rejection_reason }}</div>
        </div>
        @endif

        {{-- Reviewer info --}}
        @if($app->reviewer)
        <div class="card">
            <div class="card-header"><div class="card-title">👮 Reviewed By</div></div>
            <div class="card-body">
                <div style="font-weight:700;">{{ $app->reviewer->name }}</div>
                <div style="font-size:12px;color:var(--gray-400);">{{ $app->reviewed_at?->format('d M Y, h:i A') }}</div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
