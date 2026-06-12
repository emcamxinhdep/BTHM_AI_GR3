@extends('admin.layouts.admin')
@section('page_title', 'Dashboard')

@section('content')

{{-- Stat Cards --}}
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-calendar-alt"></i></div>
        <div>
            <div class="stat-label">Tổng lịch hẹn</div>
            <div class="stat-value">{{ number_format($stats['total_appointments']) }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-calendar-day"></i></div>
        <div>
            <div class="stat-label">Lịch hẹn hôm nay</div>
            <div class="stat-value">{{ $stats['today_appointments'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="fas fa-clock"></i></div>
        <div>
            <div class="stat-label">Chờ xác nhận</div>
            <div class="stat-value">{{ $stats['pending'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div>
            <div class="stat-label">Hoàn thành</div>
            <div class="stat-value">{{ $stats['completed'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-users"></i></div>
        <div>
            <div class="stat-label">Bệnh nhân</div>
            <div class="stat-value">{{ $stats['total_patients'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-user-md"></i></div>
        <div>
            <div class="stat-label">Bác sĩ</div>
            <div class="stat-value">{{ $stats['total_doctors'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-dollar-sign"></i></div>
        <div>
            <div class="stat-label">Doanh thu hôm nay</div>
            <div class="stat-value" style="font-size:16px">{{ number_format($stats['revenue_today']) }}đ</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-chart-line"></i></div>
        <div>
            <div class="stat-label">Doanh thu tháng</div>
            <div class="stat-value" style="font-size:16px">{{ number_format($stats['revenue_month']) }}đ</div>
        </div>
    </div>
</div>

<div class="grid-2">
    {{-- Chờ xác nhận --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-bell" style="color:#f39c12"></i> Cần xác nhận</h3>
            <a href="{{ route('admin.appointments.index', ['status'=>'pending']) }}" class="btn btn-sm btn-secondary">Xem tất cả</a>
        </div>
        <div class="card-body">
            @forelse($pendingAppointments as $a)
            <div style="padding:12px 16px;border-bottom:1px solid #f5f5f5;display:flex;align-items:center;justify-content:space-between;gap:10px">
                <div>
                    <div style="font-size:13px;font-weight:600">{{ $a->patient->name ?? '—' }}</div>
                    <div style="font-size:12px;color:#888">
                        {{ $a->doctor->name ?? '—' }} •
                        {{ \Carbon\Carbon::parse($a->appointment_date)->format('d/m/Y') }}
                        {{ substr($a->appointment_time,0,5) }}
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.appointments.confirm', ['id' => $a->id]) }}">
                    @csrf
                    <button class="btn btn-sm btn-success"><i class="fas fa-check"></i> Xác nhận</button>
                </form>
            </div>
            @empty
            <div style="padding:24px;text-align:center;color:#aaa;font-size:13px">
                <i class="fas fa-check-circle" style="font-size:28px;color:#27ae60;display:block;margin-bottom:8px"></i>
                Không có lịch chờ xác nhận
            </div>
            @endforelse
        </div>
    </div>

    {{-- Lịch hẹn hôm nay --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-calendar-day" style="color:#4f7ef8"></i> Hôm nay</h3>
            <span style="font-size:13px;color:#888">{{ now()->format('d/m/Y') }}</span>
        </div>
        <div class="card-body">
            @forelse($todayAppointments as $a)
            <div style="padding:12px 16px;border-bottom:1px solid #f5f5f5;display:flex;align-items:center;gap:12px">
                <div style="background:#eef3ff;color:#4f7ef8;border-radius:8px;padding:4px 10px;font-size:13px;font-weight:600;min-width:48px;text-align:center">
                    {{ substr($a->appointment_time,0,5) }}
                </div>
                <div style="flex:1">
                    <div style="font-size:13px;font-weight:600">{{ $a->patient->name ?? '—' }}</div>
                    <div style="font-size:12px;color:#888">{{ $a->doctor->name ?? '—' }}</div>
                </div>
                <span class="badge badge-{{ $a->status }}">
                    {{ ['pending'=>'Chờ','confirmed'=>'Đã xác nhận','completed'=>'Xong','cancelled'=>'Hủy'][$a->status] ?? $a->status }}
                </span>
            </div>
            @empty
            <div style="padding:24px;text-align:center;color:#aaa;font-size:13px">
                <i class="fas fa-calendar-times" style="font-size:28px;display:block;margin-bottom:8px"></i>
                Không có lịch hôm nay
            </div>
            @endforelse
        </div>
    </div>
</div>

<div class="grid-2">
    {{-- Biểu đồ 7 ngày --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-bar" style="color:var(--primary)"></i> Lịch hẹn 7 ngày qua</h3>
        </div>
        <div class="card-body" style="padding:20px">
            <div style="display:flex;align-items:flex-end;gap:8px;height:120px">
                @php $maxCount = max(array_column($revenueChart,'count')) ?: 1; @endphp
                @foreach($revenueChart as $d)
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px">
                    <span style="font-size:11px;color:#888">{{ $d['count'] }}</span>
                    <div style="width:100%;background:var(--primary);border-radius:4px 4px 0 0;
                         height:{{ max(4, ($d['count']/$maxCount)*100) }}px;opacity:.85"></div>
                    <span style="font-size:10px;color:#aaa">{{ $d['date'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Top bác sĩ --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-trophy" style="color:#f39c12"></i> Top bác sĩ</h3>
        </div>
        <div class="card-body">
            @foreach($topDoctors as $i => $d)
            <div style="padding:10px 16px;border-bottom:1px solid #f5f5f5;display:flex;align-items:center;gap:12px">
                <div style="width:24px;height:24px;border-radius:50%;background:{{ ['#f39c12','#95a5a6','#cd7f32','#ecf0f1','#ecf0f1'][$i] }};
                     color:{{ $i<3?'#fff':'#555' }};display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700">
                    {{ $i+1 }}
                </div>
                <div style="flex:1">
                    <div style="font-size:13px;font-weight:600">{{ $d->name }}</div>
                    <div style="font-size:12px;color:#888">{{ $d->specialty->name ?? '—' }}</div>
                </div>
                <span style="font-size:13px;font-weight:600;color:var(--primary)">{{ $d->appointments_count }} lịch</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection
