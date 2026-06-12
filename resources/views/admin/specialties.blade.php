@extends('admin.layouts.admin')

@section('page_title', 'Quản lý Chuyên khoa')

@push('styles')
<style>
    .modal-overlay {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,.5); z-index: 1000; align-items: center; justify-content: center;
    }
    .modal-overlay.show { display: flex; }
    .modal-box {
        background: #fff; border-radius: 12px; width: 100%; max-width: 480px;
        max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0,0,0,.2);
    }
    .modal-header {
        padding: 16px 20px; border-bottom: 1px solid #f0f2f5;
        display: flex; align-items: center; justify-content: space-between;
    }
    .modal-header h3 { font-size: 16px; font-weight: 600; }
    .modal-header .close-btn { cursor: pointer; font-size: 18px; color: #999; border: none; background: none; }
    .modal-body { padding: 20px; }
    .modal-footer { padding: 12px 20px; border-top: 1px solid #f0f2f5; display: flex; justify-content: flex-end; gap: 8px; }
    .img-preview { width: 56px; height: 56px; border-radius: 8px; object-fit: cover; margin-bottom: 8px; display: block; }
    .table-img { width: 48px; height: 48px; border-radius: 8px; object-fit: cover; }
</style>
@endpush

@section('content')

<div class="card-header" style="background:none;border:none;padding:0 0 16px 0;">
    <h3 style="font-size:18px;">Quản lý Chuyên khoa</h3>
    <button type="button" class="btn btn-primary" onclick="openModal('addSpecialtyModal')">
        <i class="fas fa-plus"></i> Thêm chuyên khoa
    </button>
</div>

<div class="card">
    <div class="card-body">
        <table>
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th width="80">Hình ảnh</th>
                    <th>Tên chuyên khoa</th>
                    <th>Slug</th>
                    <th>Số bác sĩ</th>
                    <th>Trạng thái</th>
                    <th width="100">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($specialties as $key => $specialty)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>
                            @if ($specialty->image)
                                <img src="{{ asset('storage/' . $specialty->image) }}" class="table-img">
                            @else
                                <div class="avatar-placeholder"><i class="fas fa-image"></i></div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:600">
                                @if ($specialty->icon)
                                    <i class="{{ $specialty->icon }}" style="color:var(--primary)"></i>
                                @endif
                                {{ $specialty->name }}
                            </div>
                            <small style="color:var(--text-muted)">{{ Str::limit($specialty->description, 50) }}</small>
                        </td>
                        <td><code>{{ $specialty->slug }}</code></td>
                        <td>{{ $specialty->doctors_count }}</td>
                        <td>
                            @if ($specialty->status == 1)
                                <span class="badge badge-active">Hoạt động</span>
                            @else
                                <span class="badge badge-inactive">Ẩn</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm" onclick="openModal('editSpecialtyModal{{ $specialty->id }}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('admin.specialty-delete') }}" method="POST" style="display:inline"
                                  onsubmit="return confirm('Bạn có chắc muốn xóa chuyên khoa này?')">
                                @csrf
                                <input type="hidden" name="id" value="{{ $specialty->id }}">
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>

                    {{-- Modal sửa --}}
                    <div class="modal-overlay" id="editSpecialtyModal{{ $specialty->id }}">
                        <div class="modal-box">
                            <form action="{{ route('admin.specialty-update', $specialty->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-header">
                                    <h3>Sửa chuyên khoa</h3>
                                    <button type="button" class="close-btn" onclick="closeModal('editSpecialtyModal{{ $specialty->id }}')">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Tên chuyên khoa</label>
                                        <input type="text" name="name" value="{{ $specialty->name }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Icon (class FontAwesome)</label>
                                        <input type="text" name="icon" value="{{ $specialty->icon }}" placeholder="fas fa-lungs">
                                    </div>
                                    <div class="form-group">
                                        <label>Mô tả</label>
                                        <textarea name="description">{{ $specialty->description }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Hình ảnh</label>
                                        @if ($specialty->image)
                                            <img src="{{ asset('storage/' . $specialty->image) }}" class="img-preview">
                                        @endif
                                        <input type="file" name="image">
                                    </div>
                                    <div class="form-group">
                                        <label>Trạng thái</label>
                                        <select name="status">
                                            <option value="1" {{ $specialty->status == 1 ? 'selected' : '' }}>Hoạt động</option>
                                            <option value="0" {{ $specialty->status == 0 ? 'selected' : '' }}>Ẩn</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" onclick="closeModal('editSpecialtyModal{{ $specialty->id }}')">Đóng</button>
                                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <tr><td colspan="7" style="text-align:center;color:var(--text-muted)">Chưa có chuyên khoa nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal thêm --}}
<div class="modal-overlay" id="addSpecialtyModal">
    <div class="modal-box">
        <form action="{{ route('admin.specialty-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h3>Thêm chuyên khoa</h3>
                <button type="button" class="close-btn" onclick="closeModal('addSpecialtyModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Tên chuyên khoa</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Icon (class FontAwesome)</label>
                    <input type="text" name="icon" placeholder="fas fa-lungs">
                </div>
                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea name="description"></textarea>
                </div>
                <div class="form-group">
                    <label>Hình ảnh</label>
                    <input type="file" name="image">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addSpecialtyModal')">Đóng</button>
                <button type="submit" class="btn btn-primary">Thêm mới</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openModal(id) {
    document.getElementById(id).classList.add('show');
}
function closeModal(id) {
    document.getElementById(id).classList.remove('show');
}
// Click ngoài modal để đóng
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) overlay.classList.remove('show');
    });
});
</script>
@endpush