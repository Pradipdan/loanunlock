<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login – First Smart Loan</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Plus Jakarta Sans',sans-serif;background:linear-gradient(135deg,#3B5BDB 0%,#1a3299 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;}
        .login-card{background:#fff;border-radius:20px;padding:40px;max-width:420px;width:100%;box-shadow:0 24px 60px rgba(0,0,0,.2);}
        .logo{display:flex;align-items:center;gap:12px;margin-bottom:32px;}
        .logo-icon{width:44px;height:44px;background:#3B5BDB;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;}
        .logo-name{font-size:20px;font-weight:800;}.logo-name span{color:#3B5BDB;}
        .login-title{font-size:24px;font-weight:800;margin-bottom:6px;}
        .login-sub{font-size:14px;color:#475467;margin-bottom:28px;}
        .form-group{margin-bottom:18px;}
        .form-label{display:block;font-size:13px;font-weight:700;color:#344054;margin-bottom:7px;}
        .form-control{width:100%;padding:12px 14px;border:1.5px solid #D0D5DD;border-radius:10px;font-family:inherit;font-size:14px;outline:none;transition:border-color .2s;}
        .form-control:focus{border-color:#3B5BDB;box-shadow:0 0 0 3px rgba(59,91,219,.1);}
        .btn-primary{display:block;width:100%;padding:14px;background:#3B5BDB;color:#fff;border:none;border-radius:10px;font-family:inherit;font-size:15px;font-weight:700;cursor:pointer;transition:background .2s;}
        .btn-primary:hover{background:#2f4ac7;}
        .alert{padding:12px 14px;border-radius:10px;font-size:13px;margin-bottom:18px;border:1px solid #FECDCA;background:#FEF3F2;color:#B42318;display:flex;gap:8px;}
        .hint{text-align:center;font-size:12px;color:#98A2B3;margin-top:20px;padding:12px;background:#F9FAFB;border-radius:10px;}
        .hint code{background:#E4E7EC;padding:2px 6px;border-radius:5px;font-size:11.5px;}
    </style>
</head>
<body>
<div class="login-card">
    <div class="logo">
        <div class="logo-icon">🏦</div>
        <div class="logo-name">First Smart<span>Loan</span></div>
    </div>
    <div class="login-title">Admin Portal</div>
    <div class="login-sub">Sign in to manage loan applications and users.</div>

    @if(session('error'))
        <div class="alert">⚠️ {{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.login.post') }}" method="POST">
        @csrf
        <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="admin@loanunlock.com" value="{{ old('email') }}" autofocus required>
            @error('email')<div style="color:#B42318;font-size:12px;margin-top:5px;">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            @error('password')<div style="color:#B42318;font-size:12px;margin-top:5px;">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn-primary">Sign In to Admin Panel</button>
    </form>

    @if(config('app.env') !== 'production')
    <div class="hint">
        🔑 Demo credentials:<br>
        <code>admin@loanunlock.com</code> / <code>Admin@123</code>
    </div>
    @endif
</div>
</body>
</html>
