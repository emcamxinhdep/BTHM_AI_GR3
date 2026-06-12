@extends('admin.layouts.admin')
@section('page_title', 'Quản lý bác sĩ')

@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
    <form method="GET" class="filters" style="margin:0">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Tên, email, phòng khám...">
        <select name="specialty_id">
            <option value="">Tất cả chuyên khoa</option>
            @foreach($specialties as $s)
                <option value="{{ $s->id }}" {{ request('specialty_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
            @endforeach
        </select>
        <select name="status">
            <option value="">Tất cả trạng thái</option>
            <option value="1" {{ request('status')==='1' ? 'selected' : '' }}>Đang hoạt động</option>
            <option value="0" {{ request('status')==='0' ? 'selected' : '' }}>Tạm ngưng</option>
        </select>
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Lọc</button>
        <a href="{{ route('admin.doctors') }}" class="btn btn-secondary">Xóa lọc</a>
    </form>
    <a href="{{ route('admin.doctor-add') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm bác sĩ
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3>Danh sách bác sĩ</h3>
        <span style="font-size:13px;color:#888">{{ $doctors->total() }} bác sĩ</span>
    </div>
    <div class="card-body">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Bác sĩ</th>
                    <th>Chuyên khoa</th>
                    <th>Phòng khám</th>
                    <th>Phí khám</th>
                    <th>Đánh giá</th>
                    <th>Lịch hẹn</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($doctors as $d)
                <tr>
                    <td style="color:#aaa;font-size:12px">{{ $d->id }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                           @if($d->avatar && file_exists(storage_path('app/public/' . $d->avatar)))
                                <img src="{{ asset('storage/' . $d->avatar) }}" class="avatar" alt="">
                            @else
                                <span class="avatar-placeholder"><i class="fas fa-user-md"></i></span>
                            @endif
                            <div>
                                <div style="font-weight:600;font-size:13px">{{ $d->name }}</div>
                                <div style="font-size:11px;color:#888">{{ $d->degree ?? '' }} • {{ $d->experience_years ?? 0 }} năm KN</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px">{{ $d->specialty->name ?? '—' }}</td>
                    <td>
                        <div style="font-size:13px">{{ $d->clinic_name ?? '—' }}</div>
                        <div style="font-size:11px;color:#888">{{ $d->clinic_district ?? '' }}, {{ $d->clinic_city ?? '' }}</div>
                    </td>
                    <td style="font-size:13px;font-weight:600">{{ number_format($d->consultation_fee ?? 0) }}đ</td>
                    <td>
                        <span style="color:#f39c12">★</span>
                        <span style="font-size:13px;font-weight:600">{{ $d->rating ?? 0 }}</span>
                        <span style="font-size:11px;color:#aaa">({{ $d->total_reviews ?? 0 }})</span>
                    </td>
                    <td style="font-size:13px;font-weight:600;color:var(--primary)">{{ $d->appointments_count ?? 0 }}</td>
                    <td>
                        <span class="badge {{ $d->status ? 'badge-active' : 'badge-inactive' }}">
                            {{ $d->status ? 'Hoạt động' : 'Tạm ngưng' }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <a href="{{ route('admin.doctor-edit', $d->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.doctor-delete') }}"
                                  onsubmit="return confirm('Xóa bác sĩ {{ $d->name }}?')">
                                @csrf
                                <input type="hidden" name="id" value="{{ $d->id }}">
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:40px;color:#aaa">
                        Không có bác sĩ nào
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($doctors->hasPages())
    <div class="pagination-wrap">{{ $doctors->links() }}</div>
    @endif
</div>
@endsection