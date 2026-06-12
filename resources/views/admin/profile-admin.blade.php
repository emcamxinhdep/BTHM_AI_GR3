@extends('admin.layouts.admin')

@section('page_title', 'Thông tin admin')

@section('content')

<div class="grid-2">
    {{-- Avatar --}}
    <div class="card">
        <div class="card-body" style="padding:24px; text-align:center;">
            <div id="crop-avatar" style="margin-bottom:16px;">
                <img id="avatarAdminPreview"
                    src="{{ $admin->avatar ? asset('admin/assets/images/user-profile/' . $admin->avatar) : asset('admin/assets/images/user-profile/default-avatar.png') }}"
                    alt="Avatar"
                    style="width:160px; height:160px; border-radius:50%; object-fit:cover; border:4px solid #f0f2f5;">
                <input type="file" name="avatarAdmin" id="avatarAdmin" style="display:none" accept="image/*">
            </div>

            <label for="avatarAdmin" id="btn_avatar" class="btn btn-success"
                action="{{ route('admin.update-avatar') }}">
                <i class="fas fa-edit"></i> Tải ảnh lên
            </label>

            <h3 style="margin-top:16px;">{{ $admin->name }}</h3>

            <ul style="list-style:none; margin-top:12px; text-align:left; padding:0 16px;">
                <li style="margin-bottom:8px; color:var(--text-muted); font-size:14px;">
                    <i class="fas fa-envelope" style="color:var(--primary); width:18px;"></i> {{ $admin->email }}
                </li>
                <li style="color:var(--text-muted); font-size:14px;">
                    <i class="fas fa-phone" style="color:var(--primary); width:18px;"></i> {{ $admin->phone }}
                </li>
            </ul>
        </div>
    </div>

    {{-- Form thông tin --}}
    <div class="card">
        <div class="card-header">
            <h3>Chỉnh sửa thông tin</h3>
        </div>
        <div class="card-body" style="padding:20px;">
            <form action="{{ route('admin.update-admin') }}" method="POST" id="formProfileAdmin">
                @csrf

                <div class="form-group">
                    <label for="name">Tên admin <span style="color:#e74c3c">*</span></label>
                    <input type="text" id="name" name="name" required
                        placeholder="Nhập tên admin" value="{{ $admin->name }}">
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu <span style="color:#e74c3c">*</span></label>
                    <input type="password" id="password" name="password" required
                        placeholder="Nhập mật khẩu" value="{{ $admin->password }}">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" required
                        placeholder="Nhập email" value="{{ $admin->email }}">
                </div>

                <div class="form-group">
                    <label for="phone">Số điện thoại</label>
                    <input id="phone" type="text" name="phone"
                        placeholder="Nhập số điện thoại" value="{{ $admin->phone }}">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Cập nhật
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('avatarAdmin').addEventListener('change', function () {
    if (!this.files || !this.files[0]) return;

    const formData = new FormData();
    formData.append('avatarAdmin', this.files[0]);
    formData.append('_token', '{{ csrf_token() }}');

    const url = document.getElementById('btn_avatar').getAttribute('action');

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.querySelectorAll('img[id="avatarAdminPreview"], img.admin-avatar-img')
                .forEach(img => img.src = data.avatar_url);
        } else {
            alert(data.message || 'Có lỗi xảy ra!');
        }
    })
    .catch(() => alert('Có lỗi xảy ra khi tải ảnh!'));
});

document.getElementById('formProfileAdmin').addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('name').value = data.data.name;
            document.getElementById('email').value = data.data.email;
            document.getElementById('phone').value = data.data.phone;
            document.getElementById('avatarAdminPreview').src = data.data.avatar ? "{{ asset('admin/assets/images/user-profile/') }}" + data.data.avatar : "{{ asset('admin/assets/images/user-profile/default-avatar.png') }}";
            alert('Cập nhật thành công!');
        } else {
            alert(data.message || 'Có lỗi xảy ra!');
        }
    })
    .catch(() => alert('Có lỗi xảy ra khi cập nhật!'));
});
</script>
@endpush