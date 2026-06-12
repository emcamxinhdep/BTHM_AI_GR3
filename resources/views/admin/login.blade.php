<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — DoctorCam</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',sans-serif; background:linear-gradient(135deg,#1a1f2e,#2d3557);
               min-height:100vh; display:flex; align-items:center; justify-content:center; }
        .login-card {
            background:#fff; border-radius:16px; padding:40px; width:380px;
            box-shadow:0 20px 60px rgba(0,0,0,.3);
        }
        .logo { text-align:center; margin-bottom:28px; }
        .logo i { font-size:36px; color:#ff6f3c; }
        .logo h2 { font-size:22px; color:#1a1f2e; margin-top:8px; }
        .logo p { font-size:13px; color:#888; }
        .form-group { margin-bottom:16px; }
        .form-group label { display:block; font-size:13px; font-weight:600; color:#555; margin-bottom:6px; }
        .input-wrap { position:relative; }
        .input-wrap i { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#aaa; font-size:14px; }
        .input-wrap input {
            width:100%; padding:10px 12px 10px 36px; border:1px solid #dde; border-radius:8px;
            font-size:14px; outline:none; transition:.2s;
        }
        .input-wrap input:focus { border-color:#ff6f3c; }
        .error { font-size:12px; color:#e74c3c; margin-top:4px; }
        .btn-login {
            width:100%; padding:12px; background:#ff6f3c; color:#fff; border:none; border-radius:8px;
            font-size:15px; font-weight:600; cursor:pointer; transition:.2s; margin-top:8px;
        }
        .btn-login:hover { background:#e55a28; }
        .alert-error { background:#f8d7da; color:#721c24; padding:10px 14px; border-radius:8px; font-size:13px; margin-bottom:16px; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="logo">
        <i class="fas fa-stethoscope"></i>
        <h2>DoctorCam Admin</h2>
        <p>Hệ thống quản trị</p>
    </div>

    @if($errors->any())
        <div class="alert-error"><i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.login-account') }}">
    @csrf
    <div class="form-group">
        <label>Tên đăng nhập</label>
        <div class="input-wrap">
            <i class="fas fa-user"></i>
            <input type="text" name="username" value="{{ old('username') }}" placeholder="admin" required autofocus>
        </div>
    </div>
    <div class="form-group">
        <label>Mật khẩu</label>
        <div class="input-wrap">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>
    </div>
    <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt"></i> Đăng nhập</button>
</form>
</div>
</body>
</html>
