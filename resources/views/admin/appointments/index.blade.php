{{--
    resources/views/admin/appointments/index.blade.php
    Giữ nguyên style/layout hiện tại của bạn.
    Thêm: nút 📧 gửi email thủ công + modal + flash messages.
--}}
@extends('admin.layouts.admin')
@section('page_title', 'Quản lý lịch hẹn')

@section('content')

{{-- Flash messages --}}
@if(session('success'))
  <div class="alert alert-success" style="margin-bottom:16px;padding:12px 16px;background:#e6f7ec;border-left:4px solid #1a7a3b;border-radius:6px;font-size:14px;color:#1a7a3b">
    ✅ {{ session('success') }}
  </div>
@endif
@if(session('error'))
  <div class="alert alert-error" style="margin-bottom:16px;padding:12px 16px;background:#fde8e8;border-left:4px solid #b91c1c;border-radius:6px;font-size:14px;color:#b91c1c">
    ❌ {{ session('error') }}
  </div>
@endif

{{-- Tab pills (giữ nguyên như cũ) --}}
<div class="tab-pills">
    <a href="{{ route('admin.appointments.index') }}"
       class="tab-pill {{ !request('status') ? 'active' : '' }}">
        Tất cả <span style="opacity:.7">({{ $statusCounts['all'] }})</span>
    </a>
    <a href="{{ route('admin.appointments.index', ['status'=>'pending']) }}"
       class="tab-pill {{ request('status')=='pending' ? 'active' : '' }}">
        ⏳ Chờ xác nhận <span style="opacity:.7">({{ $statusCounts['pending'] }})</span>
    </a>
    <a href="{{ route('admin.appointments.index', ['status'=>'confirmed']) }}"
       class="tab-pill {{ request('status')=='confirmed' ? 'active' : '' }}">
        ✅ Đã xác nhận <span style="opacity:.7">({{ $statusCounts['confirmed'] }})</span>
    </a>
    <a href="{{ route('admin.appointments.index', ['status'=>'completed']) }}"
       class="tab-pill {{ request('status')=='completed' ? 'active' : '' }}">
        🎯 Hoàn thành <span style="opacity:.7">({{ $statusCounts['completed'] }})</span>
    </a>
    <a href="{{ route('admin.appointments.index', ['status'=>'cancelled']) }}"
       class="tab-pill {{ request('status')=='cancelled' ? 'active' : '' }}">
        ❌ Đã hủy <span style="opacity:.7">({{ $statusCounts['cancelled'] }})</span>
    </a>
</div>

{{-- Filters (giữ nguyên như cũ) --}}
<form method="GET" class="filters">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Tên bệnh nhân / bác sĩ...">
    <input type="date" name="date" value="{{ request('date') }}">
    <input type="hidden" name="status" value="{{ request('status') }}">
    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Lọc</button>
    <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary">Xóa lọc</a>
</form>

<div class="card">
    <div class="card-body">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Bệnh nhân</th>
                    <th>Bác sĩ</th>
                    <th>Chuyên khoa</th>
                    <th>Ngày khám</th>
                    <th>Giờ</th>
                    <th>Phí</th>
                    <th>Thanh toán</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $a)
                <tr>
                    <td style="color:#aaa;font-size:12px">{{ $a->id }}</td>
                    <td>
                        <div style="font-weight:600;font-size:13px">{{ $a->patient->name ?? '—' }}</div>
                        <div style="font-size:11px;color:#888">{{ $a->patient->email ?? '' }}</div>
                    </td>
                    <td style="font-size:13px">{{ $a->doctor->name ?? '—' }}</td>
                    <td style="font-size:12px;color:#888">{{ $a->doctor->specialty->name ?? '—' }}</td>
                    <td style="font-size:13px">{{ \Carbon\Carbon::parse($a->appointment_date)->format('d/m/Y') }}</td>
                    <td style="font-size:13px;font-weight:600">{{ substr($a->appointment_time,0,5) }}</td>
                    <td style="font-size:13px">{{ number_format($a->fee) }}đ</td>
                    <td>
                        <span style="font-size:12px;background:#f0f2f5;padding:2px 8px;border-radius:4px">
                            {{ $a->payment_method === 'momo' ? 'MoMo' : 'Tiền mặt' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $a->status }}">
                            {{ ['pending'=>'Chờ xác nhận','confirmed'=>'Đã xác nhận','completed'=>'Hoàn thành','cancelled'=>'Đã hủy'][$a->status] ?? $a->status }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center">

                            {{-- Xác nhận (pending) — tự động gửi email confirm --}}
                            @if($a->status === 'pending')
                            <form method="POST" action="{{ route('admin.appointments.confirm', $a->id) }}">
                                @csrf @method('POST')
                                <button class="btn btn-sm btn-success" title="Xác nhận + gửi email">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            @endif

                            {{-- Hoàn thành --}}
                            @if(in_array($a->status, ['pending','confirmed']))
                            <form method="POST" action="{{ route('admin.appointments.complete', $a->id) }}">
                                @csrf @method('POST')
                                <button class="btn btn-sm btn-warning" title="Hoàn thành">
                                    <i class="fas fa-flag-checkered"></i>
                                </button>
                            </form>
                            @endif

                            {{-- Gửi email thủ công (có email) --}}
                            @if($a->patient->email ?? null)
                            <button type="button"
                                    class="btn btn-sm"
                                    style="background:#E85D26;color:#fff;border:none"
                                    title="Gửi email thông báo"
                                    onclick="openEmailModal(
                                        {{ $a->id }},
                                        '{{ addslashes($a->patient->name ?? '') }}',
                                        '{{ addslashes($a->patient->email ?? '') }}',
                                        '{{ \Carbon\Carbon::parse($a->appointment_date)->format('d/m/Y') }}',
                                        '{{ addslashes($a->doctor->name ?? '') }}'
                                    )">
                                📧
                            </button>
                            @endif

                            {{-- Hủy lịch — tự động gửi email cancel --}}
                            @if(in_array($a->status, ['pending','confirmed']))
                            <form method="POST" action="{{ route('admin.appointments.cancel', $a->id) }}"
                                  onsubmit="return confirm('Hủy lịch hẹn này và gửi email thông báo đến bệnh nhân?')">
                                @csrf @method('POST')
                                <button class="btn btn-sm btn-danger" title="Hủy + gửi email">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            @endif

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align:center;padding:40px;color:#aaa">
                        <i class="fas fa-calendar-times" style="font-size:32px;display:block;margin-bottom:10px"></i>
                        Không có lịch hẹn nào
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($appointments->hasPages())
    <div class="pagination-wrap">{{ $appointments->links() }}</div>
    @endif
</div>

{{-- ==================== MODAL GỬI EMAIL ==================== --}}
<div id="emailModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:12px;width:440px;max-width:95%;padding:28px;position:relative;max-height:90vh;overflow-y:auto">

    {{-- Close --}}
    <button onclick="closeEmailModal()" style="position:absolute;top:14px;right:16px;background:none;border:none;font-size:20px;cursor:pointer;color:#aaa">✕</button>

    <h3 style="font-size:17px;font-weight:600;margin-bottom:18px">📧 Gửi email thông báo</h3>

    {{-- Thông tin bệnh nhân --}}
    <div style="background:#fdf6f2;border-radius:8px;padding:12px 16px;margin-bottom:18px;font-size:13px;line-height:1.9">
      <div><strong>Bệnh nhân:</strong> <span id="m-patient">—</span></div>
      <div><strong>Email:</strong> <span id="m-email" style="color:#E85D26">—</span></div>
      <div><strong>Ngày khám:</strong> <span id="m-date">—</span></div>
      <div><strong>Bác sĩ:</strong> <span id="m-doctor">—</span></div>
    </div>

    <form id="emailForm" method="POST">
      @csrf

      {{-- Loại email --}}
      <div style="margin-bottom:14px">
        <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">Loại thông báo</label>
        <select name="email_type" style="width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:6px;font-size:14px" required>
          <option value="confirm">✅ Xác nhận lịch hẹn</option>
          <option value="remind" selected>⏰ Nhắc nhở lịch hẹn</option>
          <option value="cancel">❌ Thông báo hủy lịch</option>
          <option value="custom">📝 Tùy chỉnh</option>
        </select>
      </div>

      {{-- Ghi chú --}}
      <div style="margin-bottom:20px">
        <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">
          Ghi chú thêm <span style="font-weight:400;color:#999">(tùy chọn)</span>
        </label>
        <textarea name="custom_note" rows="3"
                  style="width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:6px;font-size:14px;resize:vertical"
                  placeholder="Nhập ghi chú gửi kèm email..."></textarea>
      </div>

      <div style="display:flex;gap:10px;justify-content:flex-end">
        <button type="button" onclick="closeEmailModal()"
                style="padding:9px 20px;border:1px solid #ddd;background:#fff;border-radius:6px;font-size:14px;cursor:pointer">
          Hủy
        </button>
        <button type="submit"
                style="padding:9px 24px;background:#E85D26;color:#fff;border:none;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer">
          Gửi email ngay
        </button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
function openEmailModal(id, patient, email, date, doctor) {
    document.getElementById('m-patient').textContent = patient;
    document.getElementById('m-email').textContent   = email;
    document.getElementById('m-date').textContent    = date;
    document.getElementById('m-doctor').textContent  = 'BS. ' + doctor;

    // Cập nhật action URL
    document.getElementById('emailForm').action =
        '/admin/appointments/' + id + '/send-notification';

    const modal = document.getElementById('emailModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeEmailModal() {
    document.getElementById('emailModal').style.display = 'none';
    document.body.style.overflow = '';
}

// Đóng modal khi click ngoài
document.getElementById('emailModal').addEventListener('click', function(e) {
    if (e.target === this) closeEmailModal();
});
</script>
@endpush