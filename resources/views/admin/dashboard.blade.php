@extends('layouts.admin')
@section('title','Dashboard')
@section('page-title','Dashboard')
@section('content')

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#EFF8FF;font-size:20px;">👥</div>
        <div class="stat-value" style="color:var(--blue);">{{ number_format($stats['total_users']) }}</div>
        <div class="stat-label">Total Users</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#FFFAEB;font-size:20px;">📋</div>
        <div class="stat-value" style="color:var(--orange);">{{ number_format($stats['total_applications']) }}</div>
        <div class="stat-label">Total Applications</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#ECFDF3;font-size:20px;">✅</div>
        <div class="stat-value" style="color:var(--green);">{{ number_format($stats['approved']) }}</div>
        <div class="stat-label">Approved Loans</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#FEF3F2;font-size:20px;">⏳</div>
        <div class="stat-value" style="color:var(--red);">{{ number_format($stats['pending_review']) }}</div>
        <div class="stat-label">Pending Review</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
    <div class="stat-card" style="background:linear-gradient(135deg,var(--blue),#5B7FE8);color:#fff;">
        <div style="font-size:13px;opacity:.8;margin-bottom:6px;">Total Fee Collected</div>
        <div style="font-size:28px;font-weight:800;">₹{{ number_format($stats['total_revenue']) }}</div>
        <div style="font-size:12px;opacity:.7;margin-top:4px;">Processing fees (₹299 each)</div>
    </div>
    <div class="stat-card" style="background:linear-gradient(135deg,var(--green),#34D399);color:#fff;">
        <div style="font-size:13px;opacity:.8;margin-bottom:6px;">Total Loan Value</div>
        <div style="font-size:28px;font-weight:800;">₹{{ number_format($stats['total_loan_value']) }}</div>
        <div style="font-size:12px;opacity:.7;margin-top:4px;">Across approved loans</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Recent Applications</div>
            <a href="{{ route('admin.loans.index') }}" class="btn btn-outline btn-sm">View All</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>App ID</th><th>User</th><th>Amount</th><th>Status</th><th>Date</th><th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentApplications as $app)
                    <tr>
                        <td><span style="font-family:monospace;font-size:12px;color:var(--blue);font-weight:600;">{{ $app->application_id }}</span></td>
                        <td>
                            <div style="font-weight:600;">{{ $app->user->name ?? '–' }}</div>
                            <div style="font-size:12px;color:var(--gray-400);">{{ $app->user->mobile }}</div>
                        </td>
                        <td><strong>₹{{ number_format($app->approved_amount ?? $app->requested_amount ?? 0) }}</strong></td>
                        <td>
                            @php $bc = match($app->status){'approved','disbursed'=>'badge-success','rejected'=>'badge-danger','under_review'=>'badge-warning',default=>'badge-secondary'}; @endphp
                            <span class="badge {{ $bc }}">{{ ucwords(str_replace('_',' ',$app->status)) }}</span>
                        </td>
                        <td style="color:var(--gray-400);font-size:12.5px;">{{ $app->created_at->format('d M Y') }}</td>
                        <td><a href="{{ route('admin.loans.show',$app->id) }}" class="btn btn-outline btn-sm">Review</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;color:var(--gray-400);padding:32px;">No applications yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><div class="card-title">Application Status Breakdown</div></div>
        <div class="card-body">
            @php
                $total = $stats['total_applications'] ?: 1;
                $items = [
                    ['label'=>'Under Review','val'=>$stats['pending_review'],'color'=>'var(--orange)'],
                    ['label'=>'Approved','val'=>$stats['approved'],'color'=>'var(--green)'],
                    ['label'=>'Rejected','val'=>$stats['rejected'],'color'=>'var(--red)'],
                    ['label'=>'Disbursed','val'=>$stats['disbursed'],'color'=>'var(--blue)'],
                ];
            @endphp
            @foreach($items as $item)
            <div style="margin-bottom:16px;">
                <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                    <span style="font-size:13px;font-weight:600;">{{ $item['label'] }}</span>
                    <span style="font-size:13px;font-weight:700;">{{ $item['val'] }}</span>
                </div>
                <div style="height:8px;background:var(--gray-100);border-radius:99px;overflow:hidden;">
                    <div style="height:100%;width:{{ min(100,round($item['val']/$total*100)) }}%;background:{{ $item['color'] }};border-radius:99px;transition:width .5s;"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
