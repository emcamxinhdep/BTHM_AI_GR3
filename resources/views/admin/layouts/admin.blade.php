<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — DoctorCam</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --primary: #ff6f3c;
            --primary-dark: #e55a28;
            --sidebar-bg: #1a1f2e;
            --sidebar-hover: #252b3d;
            --sidebar-active: #ff6f3c;
            --text-muted: #8892a4;
        }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar {
            width: 240px; min-height: 100vh; background: var(--sidebar-bg);
            display: flex; flex-direction: column; position: fixed; top: 0; left: 0; z-index: 100;
        }
        .sidebar-brand {
            padding: 20px 24px; border-bottom: 1px solid #2a3145;
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar-brand span { color: var(--primary); font-size: 20px; font-weight: 700; }
        .sidebar-brand small { color: var(--text-muted); font-size: 11px; display: block; }
        .sidebar-nav { padding: 16px 0; flex: 1; }
        .nav-section { padding: 8px 16px; font-size: 10px; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 1px; margin-top: 8px; }
        .nav-item a {
            display: flex; align-items: center; gap: 12px; padding: 10px 24px;
            color: #c0c8d8; text-decoration: none; font-size: 14px; transition: all .2s;
        }
        .nav-item a:hover { background: var(--sidebar-hover); color: #fff; }
        .nav-item a.active { background: var(--sidebar-active); color: #fff; border-radius: 0 24px 24px 0; margin-right: 12px; }
        .nav-item a i { width: 18px; text-align: center; }
        .sidebar-footer { padding: 16px 24px; border-top: 1px solid #2a3145; }
        .sidebar-footer form button {
            width: 100%; padding: 8px; background: transparent; border: 1px solid #2a3145;
            color: var(--text-muted); border-radius: 6px; cursor: pointer; font-size: 13px; transition: .2s;
        }
        .sidebar-footer form button:hover { background: #e74c3c; color: #fff; border-color: #e74c3c; }

        /* ── Main ── */
        .main { margin-left: 240px; flex: 1; display: flex; flex-direction: column; }
        .topbar {
            background: #fff; padding: 14px 28px; display: flex; align-items: center;
            justify-content: space-between; box-shadow: 0 1px 4px rgba(0,0,0,.08); position: sticky; top: 0; z-index: 50;
        }
        .topbar h1 { font-size: 18px; font-weight: 600; color: #1a1f2e; }
        .topbar .admin-info { display: flex; align-items: center; gap: 10px; color: #555; font-size: 14px; }
        .topbar .admin-info i { color: var(--primary); font-size: 28px; }
        .content { padding: 28px; flex: 1; }

        /* ── Cards ── */
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card {
            background: #fff; border-radius: 12px; padding: 20px;
            display: flex; align-items: center; gap: 16px; box-shadow: 0 2px 8px rgba(0,0,0,.06);
        }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .stat-icon.orange { background: #fff3ee; color: var(--primary); }
        .stat-icon.blue   { background: #eef3ff; color: #4f7ef8; }
        .stat-icon.green  { background: #eefaf4; color: #27ae60; }
        .stat-icon.yellow { background: #fffbee; color: #f39c12; }
        .stat-icon.red    { background: #ffeef0; color: #e74c3c; }
        .stat-icon.purple { background: #f3eeff; color: #9b59b6; }
        .stat-label { font-size: 12px; color: var(--text-muted); margin-bottom: 4px; }
        .stat-value { font-size: 24px; font-weight: 700; color: #1a1f2e; }

        /* ── Tables ── */
        .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,.06); overflow: hidden; margin-bottom: 20px; }
        .card-header { padding: 16px 20px; border-bottom: 1px solid #f0f2f5; display: flex; align-items: center; justify-content: space-between; }
        .card-header h3 { font-size: 15px; font-weight: 600; color: #1a1f2e; }
        .card-body { padding: 0; }
        table { width: 100%; border-collapse: collapse; }
        th { padding: 10px 16px; text-align: left; font-size: 12px; color: var(--text-muted);
             text-transform: uppercase; letter-spacing: .5px; background: #f8f9fb; border-bottom: 1px solid #eee; }
        td { padding: 12px 16px; font-size: 14px; color: #333; border-bottom: 1px solid #f5f5f5; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafbfc; }

        /* ── Badges ── */
        .badge {
            display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;
        }
        .badge-pending   { background: #fff3cd; color: #856404; }
        .badge-confirmed { background: #d1ecf1; color: #0c5460; }
        .badge-completed { background: #d4edda; color: #155724; }
        .badge-cancelled { background: #f8d7da; color: #721c24; }
        .badge-active    { background: #d4edda; color: #155724; }
        .badge-inactive  { background: #f8d7da; color: #721c24; }

        /* ── Buttons ── */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 6px;
               font-size: 13px; font-weight: 500; cursor: pointer; border: none; text-decoration: none; transition: .2s; }
        .btn-primary   { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-success   { background: #27ae60; color: #fff; }
        .btn-success:hover { background: #219150; }
        .btn-danger    { background: #e74c3c; color: #fff; }
        .btn-danger:hover  { background: #c0392b; }
        .btn-warning   { background: #f39c12; color: #fff; }
        .btn-warning:hover { background: #d68910; }
        .btn-secondary { background: #ecf0f1; color: #555; }
        .btn-secondary:hover { background: #dde4e6; }
        .btn-sm { padding: 4px 10px; font-size: 12px; }

        /* ── Filters ── */
        .filters { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 16px; align-items: center; }
        .filters input, .filters select {
            padding: 8px 12px; border: 1px solid #dde; border-radius: 8px; font-size: 13px;
            background: #fff; outline: none; transition: .2s;
        }
        .filters input:focus, .filters select:focus { border-color: var(--primary); }

        /* ── Flash ── */
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-danger  { background: #f8d7da; color: #721c24; }

        /* ── Pagination ── */
        .pagination-wrap { padding: 12px 16px; display: flex; justify-content: flex-end; }
        .pagination { display: flex; gap: 4px; }
        .pagination .page-link { padding: 6px 12px; border: 1px solid #dee2e6; border-radius: 6px;
            color: #555; text-decoration: none; font-size: 13px; }
        .pagination .page-item.active .page-link { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* ── Grid ── */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
        @media(max-width: 900px) { .grid-2, .grid-3 { grid-template-columns: 1fr; } }

        /* ── Form ── */
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 9px 12px; border: 1px solid #dde; border-radius: 8px;
            font-size: 14px; outline: none; transition: .2s; background: #fff;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: var(--primary); }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .form-error { font-size: 12px; color: #e74c3c; margin-top: 4px; }

        /* ── Tab pills ── */
        .tab-pills { display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap; }
        .tab-pill { padding: 6px 14px; border-radius: 20px; font-size: 13px; cursor: pointer;
            text-decoration: none; background: #ecf0f1; color: #555; transition: .2s; }
        .tab-pill:hover, .tab-pill.active { background: var(--primary); color: #fff; }

        /* ── Avatar ── */
        .avatar { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; }
        .avatar-placeholder { width: 36px; height: 36px; border-radius: 50%; background: #eee;
            display: inline-flex; align-items: center; justify-content: center; color: #aaa; font-size: 16px; }
    </style>
    @stack('styles')
</head>
<body>

@php
    $currentAdmin = \App\Models\admin\AdminModel::find(session('admin_id'));
    $currentAdminAvatar = ($currentAdmin && $currentAdmin->avatar)
        ? asset('admin/assets/images/user-profile/' . $currentAdmin->avatar)
        : asset('admin/assets/images/user-profile/default-avatar.png');
@endphp

{{-- Sidebar --}}
<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-stethoscope" style="color:var(--primary);font-size:22px"></i>
        <div>
            <span>DoctorCam</span>
            <small>Quản trị hệ thống</small>
        </div>
    </div>

    <nav class="sidebar-nav">
    <div class="nav-section">Tổng quan</div>
    <div class="nav-item">
        <a href="{{ route('admin.dashboard') }}"
           class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i> Dashboard
        </a>
    </div>

    <div class="nav-section">Quản lý</div>
    <div class="nav-item">
        <a href="{{ route('admin.appointments.index') }}"
           class="{{ request()->routeIs('admin.appointments.index') ? 'active' : '' }}">
            <i class="fas fa-calendar-check"></i> Lịch hẹn
            @php $pending = \App\Models\clients\Appointment::where('status','pending')->count() @endphp
            @if($pending > 0)
                <span style="margin-left:auto;background:#e74c3c;color:#fff;border-radius:10px;padding:1px 7px;font-size:11px">{{ $pending }}</span>
            @endif
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('admin.patients') }}"
           class="{{ request()->routeIs('admin.patients') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Bệnh nhân
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('admin.doctors') }}"
           class="{{ request()->routeIs('admin.doctors') ? 'active' : '' }}">
            <i class="fas fa-user-md"></i> Bác sĩ
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('admin.specialties') }}"
           class="{{ request()->routeIs('admin.specialties') ? 'active' : '' }}">
            <i class="fas fa-stethoscope"></i> Chuyên khoa
        </a>
    </div>

    <div class="nav-section">Hệ thống</div>
    <div class="nav-item">
        <a href="{{ route('admin.profile') }}"
           class="{{ request()->routeIs('admin.profile') ? 'active' : '' }}">
            <i class="fas fa-user-shield"></i> Hồ sơ admin
        </a>
    </div>
</nav>

    <div class="sidebar-footer">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
            <img class="admin-avatar-img" src="{{ $currentAdminAvatar }}" alt="Avatar"
                style="width:32px; height:32px; border-radius:50%; object-fit:cover;">
            <span style="font-size:12px;color:var(--text-muted);">{{ session('admin_email') }}</span>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit"><i class="fas fa-sign-out-alt"></i> Đăng xuất</button>
        </form>
    </div>
</aside>

{{-- Main --}}
<div class="main">
    <div class="topbar">
        <h1>@yield('page_title', 'Dashboard')</h1>
        <div class="admin-info">
            <span>{{ now()->format('d/m/Y') }}</span>
            <img class="admin-avatar-img" src="{{ $currentAdminAvatar }}" alt="Avatar"
                style="width:32px; height:32px; border-radius:50%; object-fit:cover;">
        </div>
    </div>

    <div class="content">
        @if(session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</div>

@stack('scripts')
</body>
</html>