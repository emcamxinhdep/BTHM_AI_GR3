@extends('admin.layouts.admin')
@section('page_title', isset($doctor) ? 'Chỉnh sửa bác sĩ' : 'Thêm bác sĩ')

@section('content')

<div style="max-width:800px">
    <a href="{{ route('admin.doctors') }}" class="btn btn-secondary" style="margin-bottom:16px">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-user-md" style="color:var(--primary)"></i>
                {{ isset($doctor) ? 'Chỉnh sửa: '.$doctor->name : 'Thêm bác sĩ mới' }}
            </h3>
        </div>
        <div class="card-body" style="padding:24px">
            <form method="POST" action="{{ isset($doctor) ? route('admin.doctor-update', $doctor->id) : route('admin.doctor-store') }}"
                  enctype="multipart/form-data">
                @csrf
                

                @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin:0;padding-left:16px">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
                @endif

                <div class="grid-2">
                    <div class="form-group">
                        <label>Họ tên bác sĩ *</label>
                        <input type="text" name="name" value="{{ old('name', $doctor->name ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Chuyên khoa *</label>
                        <select name="specialty_id" required>
                            <option value="">-- Chọn chuyên khoa --</option>
                            @foreach($specialties as $s)
                                <option value="{{ $s->id }}" {{ old('specialty_id', $doctor->specialty_id ?? '') == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" value="{{ old('email', $doctor->email ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" name="phone" value="{{ old('phone', $doctor->phone ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label>Học vị (ThS.BS, TS.BS...)</label>
                        <input type="text" name="degree" value="{{ old('degree', $doctor->degree ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label>Số năm kinh nghiệm</label>
                        <input type="number" name="experience_years" min="0" value="{{ old('experience_years', $doctor->experience_years ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label>Phí khám (VNĐ)</label>
                        <input type="number" name="consultation_fee" min="0" value="{{ old('consultation_fee', $doctor->consultation_fee ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label>Trạng thái *</label>
                        <select name="status" required>
                            <option value="1" {{ old('status', $doctor->status ?? 1) == 1 ? 'selected' : '' }}>Đang hoạt động</option>
                            <option value="0" {{ old('status', $doctor->status ?? 1) == 0 ? 'selected' : '' }}>Tạm ngưng</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Tên phòng khám</label>
                    <input type="text" name="clinic_name" value="{{ old('clinic_name', $doctor->clinic_name ?? '') }}">
                </div>
                <div class="grid-3">
                    <div class="form-group">
                        <label>Địa chỉ phòng khám</label>
                        <input type="text" name="clinic_address" value="{{ old('clinic_address', $doctor->clinic_address ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label>Quận/Huyện</label>
                        <input type="text" name="clinic_district" value="{{ old('clinic_district', $doctor->clinic_district ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label>Thành phố</label>
                        <input type="text" name="clinic_city" value="{{ old('clinic_city', $doctor->clinic_city ?? '') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Lịch làm việc (JSON)</label>
                    <textarea name="working_hours" placeholder='{"Mon":"8-12,14-17","Tue":"8-12","Sat":"8-12"}'>{{ old('working_hours', isset($doctor) ? $doctor->working_hours : '') }}</textarea>
                    <div style="font-size:11px;color:#aaa;margin-top:4px">
                        Định dạng: Mon/Tue/Wed/Thu/Fri/Sat/Sun → giờ bắt đầu-kết thúc, phân cách bằng dấu phẩy nếu nhiều ca
                    </div>
                </div>

                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea name="description">{{ old('description', $doctor->description ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Ảnh đại diện</label>
                    @if(isset($doctor) && $doctor->avatar)
                        <div style="margin-bottom:8px">
                            <img src="{{ asset('storage/' . $doctor->avatar) }}" style="width:60px;height:60px;border-radius:50%;object-fit:cover">
                        </div>
                    @endif
                    <input type="file" name="avatar" accept="image/*">
                </div>

                <div style="display:flex;gap:10px;margin-top:8px">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ isset($doctor) ? 'Cập nhật' : 'Thêm bác sĩ' }}
                    </button>
                    <a href="{{ route('admin.doctors') }}" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
