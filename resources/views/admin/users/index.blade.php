@extends('layouts.admin')
@section('title','Users')
@section('page-title','User Management')
@section('content')

<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px 20px;">
        <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
            <div style="flex:1;min-width:200px;">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name, mobile, PAN, email..." value="{{ request('search') }}">
            </div>
            <div style="min-width:140px;">
                <label class="form-label">Filter</label>
                <select name="status" class="form-control">
                    <option value="">All Users</option>
                    <option value="verified" {{ request('status')==='verified'?'selected':'' }}>Verified</option>
                    <option value="blocked" {{ request('status')==='blocked'?'selected':'' }}>Blocked</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Clear</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">{{ $users->total() }} User(s)</div>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>User</th><th>Mobile</th><th>PAN</th><th>State</th><th>Applications</th><th>Verified</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $user->name ?? '—' }}</div>
                        <div style="font-size:12px;color:var(--gray-400);">{{ $user->email ?? '' }}</div>
                    </td>
                    <td style="font-family:monospace;font-weight:600;">{{ $user->mobile }}</td>
                    <td style="font-family:monospace;font-size:12.5px;">{{ $user->pan_number ?? '—' }}</td>
                    <td>{{ $user->state ?? '—' }}</td>
                    <td><span class="badge badge-info">{{ $user->loan_applications_count }}</span></td>
                    <td>{{ $user->is_verified ? '✅' : '❌' }}</td>
                    <td>
                        @if($user->is_blocked)
                            <span class="badge badge-danger">Blocked</span>
                        @else
                            <span class="badge badge-success">Active</span>
                        @endif
                    </td>
                    <td style="font-size:12.5px;color:var(--gray-400);">{{ $user->created_at->format('d M Y') }}</td>
                    <td style="display:flex;gap:6px;">
                        <a href="{{ route('admin.users.show',$user->id) }}" class="btn btn-outline btn-sm">View</a>
                        @if($user->is_blocked)
                            <form action="{{ route('admin.users.unblock',$user->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Unblock</button>
                            </form>
                        @else
                            <form action="{{ route('admin.users.block',$user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Block this user?')">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Block</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center;color:var(--gray-400);padding:40px;">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div style="padding:16px 20px;">{{ $users->links() }}</div>
    @endif
</div>
@endsection
