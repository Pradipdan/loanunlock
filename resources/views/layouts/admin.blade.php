<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title','Admin') – First Smart Loan Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root{--blue:#3B5BDB;--blue-dark:#2f4ac7;--blue-light:#EEF2FF;--orange:#F09210;--green:#12B76A;--red:#F04438;--sidebar-w:240px;--gray-50:#F9FAFB;--gray-100:#F2F4F7;--gray-200:#E4E7EC;--gray-400:#98A2B3;--gray-600:#475467;--gray-900:#101828;}
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--gray-50);color:var(--gray-900);font-size:14px;-webkit-font-smoothing:antialiased;}
        /* Sidebar */
        .sidebar{position:fixed;top:0;left:0;width:var(--sidebar-w);height:100vh;background:#fff;border-right:1px solid var(--gray-200);display:flex;flex-direction:column;z-index:100;overflow-y:auto;}
        .sidebar-logo{padding:24px 20px 16px;border-bottom:1px solid var(--gray-100);display:flex;align-items:center;gap:10px;}
        .sidebar-logo-icon{width:36px;height:36px;background:var(--blue);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;}
        .sidebar-logo-name{font-weight:800;font-size:16px;color:var(--gray-900);}
        .sidebar-logo-name span{color:var(--blue);}
        .sidebar-section{padding:16px 12px 8px;font-size:11px;font-weight:700;text-transform:uppercase;color:var(--gray-400);letter-spacing:.6px;}
        .sidebar-nav{padding:0 8px;}
        .nav-link{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;color:var(--gray-600);text-decoration:none;font-weight:600;font-size:13.5px;transition:all .15s;margin-bottom:2px;}
        .nav-link:hover,.nav-link.active{background:var(--blue-light);color:var(--blue);}
        .nav-link i{font-size:17px;width:20px;text-align:center;}
        .nav-badge{margin-left:auto;background:var(--red);color:#fff;font-size:11px;font-weight:700;padding:2px 7px;border-radius:99px;}
        .sidebar-footer{margin-top:auto;padding:16px 12px;border-top:1px solid var(--gray-100);}
        .admin-info{display:flex;align-items:center;gap:10px;padding:10px;}
        .admin-avatar{width:34px;height:34px;border-radius:50%;background:var(--blue);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;}
        .admin-name{font-weight:700;font-size:13px;}
        .admin-role{font-size:11px;color:var(--gray-400);}
        /* Main */
        .main-content{margin-left:var(--sidebar-w);min-height:100vh;display:flex;flex-direction:column;}
        .topbar{background:#fff;border-bottom:1px solid var(--gray-200);padding:16px 28px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;}
        .topbar-title{font-size:18px;font-weight:800;}
        .topbar-actions{display:flex;align-items:center;gap:12px;}
        .page-content{padding:28px;flex:1;}
        /* Cards */
        .card{background:#fff;border-radius:14px;border:1px solid var(--gray-200);overflow:hidden;}
        .card-header{padding:18px 20px;border-bottom:1px solid var(--gray-100);display:flex;align-items:center;justify-content:space-between;}
        .card-title{font-weight:700;font-size:15px;}
        .card-body{padding:20px;}
        /* Stats Grid */
        .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;}
        .stat-card{background:#fff;border-radius:14px;padding:20px;border:1px solid var(--gray-200);}
        .stat-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:12px;}
        .stat-value{font-size:26px;font-weight:800;line-height:1;}
        .stat-label{font-size:12.5px;color:var(--gray-600);margin-top:5px;font-weight:500;}
        /* Table */
        .table-wrap{overflow-x:auto;}
        table{width:100%;border-collapse:collapse;}
        th{background:var(--gray-50);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--gray-600);padding:12px 16px;text-align:left;border-bottom:1px solid var(--gray-200);}
        td{padding:14px 16px;border-bottom:1px solid var(--gray-100);font-size:13.5px;vertical-align:middle;}
        tr:last-child td{border-bottom:none;}
        tr:hover td{background:var(--gray-50);}
        /* Badges */
        .badge{display:inline-flex;align-items:center;padding:4px 10px;border-radius:99px;font-size:12px;font-weight:700;}
        .badge-success{background:#ECFDF3;color:#027A48;}
        .badge-warning{background:#FFFAEB;color:#B54708;}
        .badge-danger{background:#FEF3F2;color:#B42318;}
        .badge-info{background:#EFF8FF;color:#175CD3;}
        .badge-secondary{background:var(--gray-100);color:var(--gray-600);}
        /* Buttons */
        .btn{display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:9px;font-family:inherit;font-size:13.5px;font-weight:600;cursor:pointer;border:none;text-decoration:none;transition:all .15s;}
        .btn-primary{background:var(--blue);color:#fff;}.btn-primary:hover{background:var(--blue-dark);color:#fff;}
        .btn-success{background:var(--green);color:#fff;}
        .btn-danger{background:var(--red);color:#fff;}
        .btn-outline{background:#fff;color:var(--gray-600);border:1px solid var(--gray-200);}.btn-outline:hover{background:var(--gray-50);}
        .btn-sm{padding:6px 12px;font-size:12.5px;}
        /* Form */
        .form-label{display:block;font-weight:600;font-size:13px;margin-bottom:6px;color:var(--gray-600);}
        .form-control{width:100%;padding:10px 13px;border:1.5px solid var(--gray-200);border-radius:9px;font-family:inherit;font-size:14px;color:var(--gray-900);outline:none;transition:border-color .2s;}
        .form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(59,91,219,.1);}
        textarea.form-control{resize:vertical;min-height:90px;}
        .form-group{margin-bottom:16px;}
        .form-row{display:grid;gap:16px;}
        .form-row.cols-2{grid-template-columns:1fr 1fr;}
        .form-row.cols-3{grid-template-columns:1fr 1fr 1fr;}
        /* Alert */
        .alert{padding:12px 16px;border-radius:10px;font-size:13.5px;margin-bottom:18px;display:flex;align-items:flex-start;gap:8px;}
        .alert-success{background:#ECFDF3;color:#027A48;border:1px solid #A9EFC5;}
        .alert-error{background:#FEF3F2;color:#B42318;border:1px solid #FECDCA;}
        /* Pagination */
        .pagination{display:flex;align-items:center;gap:6px;margin-top:20px;}
        .pagination a,.pagination span{padding:7px 12px;border-radius:8px;font-size:13px;font-weight:600;border:1px solid var(--gray-200);color:var(--gray-600);text-decoration:none;}
        .pagination .active span,.pagination a:hover{background:var(--blue);color:#fff;border-color:var(--blue);}
        /* Modal */
        .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:999;display:none;align-items:center;justify-content:center;}
        .modal-overlay.open{display:flex;}
        .modal-box{background:#fff;border-radius:18px;padding:28px;max-width:480px;width:90%;max-height:90vh;overflow-y:auto;}
        .modal-title{font-size:18px;font-weight:800;margin-bottom:4px;}
        .modal-subtitle{font-size:13px;color:var(--gray-600);margin-bottom:20px;}
        @media(max-width:768px){
            .sidebar{transform:translateX(-100%);}.main-content{margin-left:0;}.stats-grid{grid-template-columns:1fr 1fr;}
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="sidebar">
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">🏦</div>
        <div class="sidebar-logo-name">First Smart<span>Loan</span></div>
    </div>

    <div class="sidebar-nav" style="margin-top:8px;">
        <div class="sidebar-section">Main</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">
            <i class="bi bi-grid-fill"></i> Dashboard
        </a>

        <div class="sidebar-section">Loan Management</div>
        <a href="{{ route('admin.loans.index') }}" class="nav-link {{ request()->routeIs('admin.loans*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text-fill"></i> All Applications
            @php $pending = \App\Models\LoanApplication::where('status','under_review')->count(); @endphp
            @if($pending) <span class="nav-badge">{{ $pending }}</span> @endif
        </a>
        <a href="{{ route('admin.loans.index',['status'=>'under_review']) }}" class="nav-link {{ request()->get('status')==='under_review' ? 'active' : '' }}">
            <i class="bi bi-hourglass-split"></i> Pending Review
        </a>
        <a href="{{ route('admin.loans.index',['status'=>'approved']) }}" class="nav-link">
            <i class="bi bi-check-circle-fill"></i> Approved
        </a>
        <a href="{{ route('admin.loans.index',['status'=>'disbursed']) }}" class="nav-link">
            <i class="bi bi-cash-stack"></i> Disbursed
        </a>

        <div class="sidebar-section">Users</div>
        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> All Users
        </a>

        <div class="sidebar-section">Reports</div>
        <a href="{{ route('admin.reports') }}" class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-fill"></i> Reports
        </a>
        <a href="{{ route('admin.reports.export') }}" class="nav-link">
            <i class="bi bi-download"></i> Export CSV
        </a>

        <div class="sidebar-section">System</div>
        <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
            <i class="bi bi-gear-fill"></i> Settings
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="admin-info">
            <div class="admin-avatar">{{ strtoupper(substr(session('admin_name','A'),0,1)) }}</div>
            <div>
                <div class="admin-name">{{ session('admin_name','Admin') }}</div>
                <div class="admin-role">{{ ucwords(str_replace('_',' ',session('admin_role','admin'))) }}</div>
            </div>
        </div>
        <a href="{{ route('admin.logout') }}" class="nav-link" style="color:var(--red);">
            <i class="bi bi-box-arrow-left"></i> Logout
        </a>
    </div>
</div>

<div class="main-content">
    <div class="topbar">
        <div class="topbar-title">@yield('page-title','Dashboard')</div>
        <div class="topbar-actions">
            <span style="font-size:12px;color:var(--gray-400);">{{ now()->format('d M Y, h:i A') }}</span>
            <a href="{{ route('admin.loans.index',['status'=>'under_review']) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-hourglass-split"></i> {{ $pending ?? 0 }} Pending
            </a>
        </div>
    </div>

    <div class="page-content">
        @if(session('success'))
            <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error"><i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}</div>
        @endif
        @yield('content')
    </div>
</div>

@stack('scripts')
</body>
</html>
