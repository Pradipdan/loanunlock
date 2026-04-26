@extends('layouts.admin')
@section('title','Applications')
@section('page-title','Loan Applications')
@section('content')

{{-- Filters --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px 20px;">
        <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
            <div style="flex:1;min-width:200px;">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name, mobile, PAN, App ID..." value="{{ request('search') }}">
            </div>
            <div style="min-width:160px;">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    @foreach(['draft','personal_filled','eligibility_checked','payment_pending','payment_done','under_review','approved','rejected','disbursed','closed'] as $s)
                        <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.loans.index') }}" class="btn btn-outline">Clear</a>
        </form>
    </div>
</div>

{{-- Status Quick Tabs --}}
<div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;">
    @foreach(['under_review'=>'⏳ Pending','approved'=>'✅ Approved','rejected'=>'❌ Rejected','disbursed'=>'💰 Disbursed'] as $s => $label)
    <a href="{{ route('admin.loans.index',['status'=>$s]) }}"
       style="padding:7px 14px;border-radius:99px;font-size:13px;font-weight:600;text-decoration:none;border:1.5px solid {{ request('status')===$s ? 'var(--blue)' : 'var(--gray-200)' }};background:{{ request('status')===$s ? 'var(--blue-light)' : '#fff' }};color:{{ request('status')===$s ? 'var(--blue)' : 'var(--gray-600)' }};">
        {{ $label }}
        <span style="background:rgba(0,0,0,.08);padding:1px 7px;border-radius:99px;font-size:11px;margin-left:4px;">{{ $statusCounts[$s] ?? 0 }}</span>
    </a>
    @endforeach
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">{{ $applications->total() }} Application(s)</div>
        <a href="{{ route('admin.reports.export') }}" class="btn btn-outline btn-sm"><i class="bi bi-download"></i> Export CSV</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>App ID</th><th>User</th><th>Amount Req.</th><th>Approved</th><th>EMI</th><th>Employment</th><th>Status</th><th>Applied</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                <tr>
                    <td><a href="{{ route('admin.loans.show',$app->id) }}" style="font-family:monospace;font-size:12px;color:var(--blue);font-weight:700;text-decoration:none;">{{ $app->application_id }}</a></td>
                    <td>
                        <div style="font-weight:600;font-size:13.5px;">{{ $app->user->name ?? '—' }}</div>
                        <div style="font-size:12px;color:var(--gray-400);">{{ $app->user->mobile }}</div>
                        @if($app->user->pan_number)<div style="font-size:11px;color:var(--gray-400);font-family:monospace;">{{ $app->user->pan_number }}</div>@endif
                    </td>
                    <td>₹{{ number_format($app->requested_amount ?? 0) }}</td>
                    <td>{{ $app->approved_amount ? '₹'.number_format($app->approved_amount) : '—' }}</td>
                    <td>{{ $app->emi_amount ? '₹'.number_format($app->emi_amount) : '—' }}</td>
                    <td>{{ $app->employment_type ? ucfirst($app->employment_type) : '—' }}</td>
                    <td>
                        @php $bc = match($app->status){'approved','disbursed'=>'badge-success','rejected'=>'badge-danger','under_review'=>'badge-warning','payment_done'=>'badge-info',default=>'badge-secondary'}; @endphp
                        <span class="badge {{ $bc }}">{{ ucwords(str_replace('_',' ',$app->status)) }}</span>
                    </td>
                    <td style="font-size:12.5px;color:var(--gray-400);">{{ $app->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('admin.loans.show',$app->id) }}" class="btn btn-outline btn-sm">Review</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center;color:var(--gray-400);padding:40px;">No applications found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($applications->hasPages())
    <div style="padding:16px 20px;">
        {{ $applications->links() }}
    </div>
    @endif
</div>
@endsection
