@extends('layouts.admin')
@section('title','Reports')
@section('page-title','Reports & Analytics')
@section('content')

<div class="stats-grid" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:#ECFDF3;">💰</div>
        <div class="stat-value" style="color:var(--green);">₹{{ number_format($paymentStats['total_collected']) }}</div>
        <div class="stat-label">Total Fee Collected</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#EFF8FF;">📲</div>
        <div class="stat-value" style="color:var(--blue);">{{ $paymentStats['upi'] }}</div>
        <div class="stat-label">UPI Payments</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#FFFAEB;">💳</div>
        <div class="stat-value" style="color:var(--orange);">{{ $paymentStats['card'] }}</div>
        <div class="stat-label">Card Payments</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#FEF3F2;">🏦</div>
        <div class="stat-value" style="color:var(--red);">{{ $paymentStats['netbanking'] }}</div>
        <div class="stat-label">Net Banking</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">All Applications Report</div>
        <a href="{{ route('admin.reports.export') }}" class="btn btn-primary btn-sm"><i class="bi bi-download"></i> Export CSV</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>App ID</th><th>User</th><th>Mobile</th><th>Amount</th><th>EMI</th><th>Status</th><th>Payment</th><th>Applied</th></tr></thead>
            <tbody>
                @forelse($applications as $app)
                <tr>
                    <td style="font-family:monospace;font-size:12px;color:var(--blue);font-weight:600;">{{ $app->application_id }}</td>
                    <td>{{ $app->user->name ?? '—' }}</td>
                    <td style="font-family:monospace;font-size:12.5px;">{{ $app->user->mobile ?? '' }}</td>
                    <td>₹{{ number_format($app->approved_amount ?? $app->requested_amount ?? 0) }}</td>
                    <td>{{ $app->emi_amount ? '₹'.number_format($app->emi_amount) : '—' }}</td>
                    <td>
                        @php $bc=match($app->status){'approved','disbursed'=>'badge-success','rejected'=>'badge-danger','under_review'=>'badge-warning',default=>'badge-secondary'}; @endphp
                        <span class="badge {{ $bc }}">{{ ucwords(str_replace('_',' ',$app->status)) }}</span>
                    </td>
                    <td>
                        @php $p=$app->payments->where('status','success')->first(); @endphp
                        @if($p) <span class="badge badge-success">₹{{ $p->amount }} ({{ strtoupper($p->method) }})</span>
                        @else <span class="badge badge-secondary">—</span> @endif
                    </td>
                    <td style="font-size:12px;color:var(--gray-400);">{{ $app->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--gray-400);">No data yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($applications->hasPages())
    <div style="padding:16px 20px;">{{ $applications->links() }}</div>
    @endif
</div>
@endsection
