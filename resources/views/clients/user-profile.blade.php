@include('clients.blocks.header')

<div class="user-profile">
    <div class="container-xl px-4 mt-4 mb-5">

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <!-- ============ CỘT TRÁI: AVATAR ============ -->
            <div class="col-xl-4">
                <div class="card mb-4 mb-xl-0">
                    <div class="card-header">Ảnh đại diện</div>
                    <div class="card-body text-center">
                        <img id="avatarPreview" class="img-account-profile rounded-circle mb-2"
                            src="{{ $user->avatar ? asset('clients/assets/images/upload/' . $user->avatar) : asset('clients/assets/images/default-avatar.png') }}"
                            style="width:160px; height:160px; object-fit:cover;" alt="Ảnh đại diện">

                        <div class="small font-italic text-muted mb-2">JPG hoặc PNG không lớn hơn 5 MB</div>
                        <div id="avatarUploadError" class="text-danger small mb-2"></div>

                        <input type="file" name="avatar" id="avatar" style="display: none" accept="image/jpeg,image/png">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" class="__token">
                        <input type="hidden" value="{{ route('change-avatar') }}" class="label_avatar">

                        <label for="avatar" class="btn btn-primary" id="avatarLabel">Tải ảnh lên</label>
                    </div>
                </div>

                <div class="card mb-4 mb-xl-0 mt-4">
                    <button class="btn btn-primary w-100" id="update_password_profile"
                            type="button" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                        Đổi mật khẩu
                    </button>
                </div>
            </div>

            <!-- ============ CỘT PHẢI: THÔNG TIN TÀI KHOẢN ============ -->
            <div class="col-xl-8">
                <div class="card mb-4">
                    <div class="card-header">Thông tin tài khoản</div>
                    <div class="card-body">
                        <form action="{{ route('update-user-profile') }}" method="POST" name="updateUser" class="updateUser">
                            @csrf

                            <div class="row gx-3 mb-3">
                                <div class="col-md-12">
                                    <label class="small mb-1" for="inputFullName">Họ và tên</label>
                                    <input class="form-control" id="inputFullName" name="name" type="text"
                                        placeholder="Họ và tên" value="{{ old('name', $user->name) }}" required>
                                </div>
                            </div>

                            <div class="row gx-3 mb-3">
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputBirthday">Ngày sinh</label>
                                    <input class="form-control" id="inputBirthday" name="birthday" type="date"
                                        value="{{ old('birthday', $user->birthday) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputGender">Giới tính</label>
                                    <select class="form-control" id="inputGender" name="gender">
                                        <option value="">-- Chọn --</option>
                                        <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Nam</option>
                                        <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Nữ</option>
                                        <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Khác</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row gx-3 mb-3">
                                <div class="col-md-12">
                                    <label class="small mb-1" for="inputLocation">Địa chỉ</label>
                                    <input class="form-control" id="inputLocation" name="address" type="text"
                                        placeholder="Địa chỉ" value="{{ old('address', $user->address) }}">
                                </div>
                            </div>

                            <div class="row gx-3 mb-3">
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputDistrict">Quận/Huyện</label>
                                    <input class="form-control" id="inputDistrict" name="district" type="text"
                                        placeholder="Quận/Huyện" value="{{ old('district', $user->district) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputCity">Thành phố</label>
                                    <input class="form-control" id="inputCity" name="city" type="text"
                                        placeholder="Thành phố" value="{{ old('city', $user->city) }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1" for="inputEmailAddress">Email</label>
                                <input class="form-control" id="inputEmailAddress" type="email" placeholder="Email"
                                    value="{{ $user->email }}" disabled>
                                <div class="form-text">Email không thể thay đổi.</div>
                            </div>

                            <div class="row gx-3 mb-3">
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputPhone">Số điện thoại</label>
                                    <input class="form-control" id="inputPhone" name="phone" type="tel"
                                        placeholder="Số điện thoại" value="{{ old('phone', $user->phone) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputBloodType">Nhóm máu</label>
                                    <select class="form-control" id="inputBloodType" name="blood_type">
                                        <option value="">-- Chọn --</option>
                                        @foreach (['A', 'B', 'O', 'AB'] as $bt)
                                            <option value="{{ $bt }}" {{ old('blood_type', $user->blood_type) == $bt ? 'selected' : '' }}>{{ $bt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1" for="inputMedicalHistory">Tiền sử bệnh</label>
                                <textarea class="form-control" id="inputMedicalHistory" name="medical_history" rows="3"
                                    placeholder="Tiền sử bệnh, dị ứng, thuốc đang dùng...">{{ old('medical_history', $user->medical_history) }}</textarea>
                            </div>

                            <button class="btn btn-primary" type="submit" id="update_profile">Lưu thông tin</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============ MODAL ĐỔI MẬT KHẨU ============ -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('change-password') }}" method="POST" class="change_password_profile">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Đổi mật khẩu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="invalid-feedback d-block" id="validate_password"></div>

                    <div class="mb-3">
                        <label class="small mb-1">Mật khẩu hiện tại</label>
                        <input class="form-control" id="inputOldPass" name="current_password" type="password"
                            placeholder="Nhập mật khẩu cũ" required>
                    </div>
                    <div class="mb-3">
                        <label class="small mb-1">Mật khẩu mới</label>
                        <input class="form-control" id="inputNewPass" name="password" type="password"
                            placeholder="Nhập mật khẩu mới" minlength="6" required>
                    </div>
                    <div class="mb-3">
                        <label class="small mb-1">Xác nhận mật khẩu mới</label>
                        <input class="form-control" id="inputNewPassConfirm" name="password_confirmation" type="password"
                            placeholder="Nhập lại mật khẩu mới" minlength="6" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('clients.blocks.footer')

<script>
// Chạy sớm nhất có thể - trước cả DOMContentLoaded
(function() {
    function blockJqueryOnForm() {
        var form = document.querySelector('form[name="updateUser"]');
        if (!form) return;

        // Đánh dấu để nhận ra
        form.setAttribute('data-vanilla-submit', 'true');

        // Gắn listener ở capture phase với priority cao nhất
        form.addEventListener('submit', function(e) {
            // Không preventDefault - cho submit bình thường
            e.stopImmediatePropagation();
        }, true);

        // Neutralize jQuery nếu đã load
        if (window.jQuery) {
            jQuery(form).off('submit');
        }
    }

    // Chạy ngay
    blockJqueryOnForm();

    // Chạy lại sau khi DOM ready (phòng trường hợp jQuery gắn listener sau)
    document.addEventListener('DOMContentLoaded', function() {
        blockJqueryOnForm();
        // Gỡ jQuery listener một lần nữa sau khi mọi script đã chạy
        setTimeout(function() {
            if (window.jQuery) {
                jQuery('form[name="updateUser"]').off('submit');
            }
        }, 500);
    });
})();

// ---- Avatar upload (giữ nguyên) ----
document.addEventListener('DOMContentLoaded', function () {
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatarPreview');
    const avatarLabel = document.getElementById('avatarLabel');
    const avatarError = document.getElementById('avatarUploadError');
    const csrfToken = document.querySelector('.__token').value;
    const changeAvatarUrl = document.querySelector('.label_avatar').value;

    if (avatarInput) {
        avatarInput.addEventListener('change', function () {
            const file = this.files[0];
            avatarError.textContent = '';
            if (!file) return;

            if (!['image/jpeg', 'image/png'].includes(file.type)) {
                avatarError.textContent = 'Chỉ hỗ trợ ảnh JPG hoặc PNG.';
                avatarInput.value = '';
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                avatarError.textContent = 'Ảnh không được lớn hơn 5MB.';
                avatarInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = e => avatarPreview.src = e.target.result;
            reader.readAsDataURL(file);

            const originalLabel = avatarLabel.textContent;
            avatarLabel.textContent = 'Đang tải lên...';
            avatarLabel.classList.add('disabled');

            const formData = new FormData();
            formData.append('avatar', file);
            formData.append('_token', csrfToken);

            fetch(changeAvatarUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                body: formData
            })
            .then(res => res.json().catch(() => ({})))
            .then(() => { window.location.reload(); })
            .catch(() => {
                avatarError.textContent = 'Có lỗi xảy ra khi tải ảnh lên, vui lòng thử lại.';
                avatarLabel.textContent = originalLabel;
                avatarLabel.classList.remove('disabled');
            });
        });
    }

    // Disable button khi submit để tránh gửi nhiều lần
var profileForm = document.querySelector('form[name="updateUser"]');
if (profileForm) {
    profileForm.addEventListener('submit', function() {
        var btn = document.getElementById('update_profile');
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Đang lưu...';
        }
    }, true);
}
});
</script>