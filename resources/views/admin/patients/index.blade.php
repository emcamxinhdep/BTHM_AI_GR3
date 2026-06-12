@extends('admin.layouts.admin')
@section('page_title', 'Quản lý bệnh nhân')

@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
    <form method="GET" class="filters" style="margin:0">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Tên, email, số điện thoại...">
        <select name="status">
            <option value="">Tất cả trạng thái</option>
            <option value="1" {{ request('status')==='1' ? 'selected' : '' }}>Đang hoạt động</option>
            <option value="0" {{ request('status')==='0' ? 'selected' : '' }}>Đã khóa</option>
        </select>
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Lọc</button>
        <a href="{{ route('admin.patients') }}" class="btn btn-secondary">Xóa lọc</a>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3>Danh sách bệnh nhân</h3>
        <span style="font-size:13px;color:#888">{{ $patients->total() }} bệnh nhân</span>
    </div>
    <div class="card-body">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Bệnh nhân</th>
                    <th>Số điện thoại</th>
                    <th>Giới tính</th>
                    <th>Tổng lịch hẹn</th>
                    <th>Ngày đăng ký</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $p)
                <tr>
                    <td style="color:#aaa;font-size:12px">{{ $p->id }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            @if($p->avatar)
                                <img src="{{ asset('storage/'.$p->avatar) }}" class="avatar" alt="">
                            @else
                                <span class="avatar-placeholder"><i class="fas fa-user"></i></span>
                            @endif
                            <div>
                                <div style="font-weight:600;font-size:13px">{{ $p->name ?? $p->fullName ?? 'Unnamed' }}</div>
                                <div style="font-size:11px;color:#888">{{ $p->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px">{{ $p->phone ?? $p->phoneNumber ?? '—' }}</td>
                    <td style="font-size:13px">
                        {{ $p->gender === 'male' ? '👨 Nam' : ($p->gender === 'female' ? '👩 Nữ' : '—') }}
                    </td>
                    <td style="font-size:13px;font-weight:600;color:var(--primary)">{{ $p->appointments_count ?? 0 }}</td>
                    <td style="font-size:12px;color:#888">{{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge {{ $p->status ? 'badge-active' : 'badge-inactive' }}">
                            {{ $p->status ? 'Hoạt động' : 'Đã khóa' }}
                        </span>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.status-patient') }}"
                              onsubmit="return confirm('{{ $p->status ? 'Khóa' : 'Mở khóa' }} tài khoản này?')">
                            @csrf
                            <input type="hidden" name="patientId" value="{{ $p->id }}">
                            <input type="hidden" name="status" value="{{ $p->status ? '0' : '1' }}">
                            <button class="btn btn-sm {{ $p->status ? 'btn-danger' : 'btn-success' }}">
                                <i class="fas fa-{{ $p->status ? 'ban' : 'unlock' }}"></i>
                                {{ $p->status ? 'Khóa' : 'Mở khóa' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:#aaa">
                        Không có bệnh nhân nào
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($patients->hasPages())
    <div class="pagination-wrap">{{ $patients->links() }}</div>
    @endif
</div>
@endsection