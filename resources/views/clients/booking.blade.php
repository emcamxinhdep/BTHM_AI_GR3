@include('clients.blocks.header')
@include('clients.blocks.banner')

<section class="container" style="margin-top:50px; margin-bottom:100px">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
            <a href="{{ route('appointments.index') }}" class="alert-link">Xem lịch hẹn của tôi</a>
        </div>
    @endif

    @if (session('conflict'))
        <div class="alert alert-warning">
            <strong>Khung giờ bạn chọn đã có người đặt!</strong>
            <p>Vui lòng chọn một trong các thời gian thay thế dưới đây hoặc chọn giờ khác:</p>
            <div class="d-flex flex-wrap gap-2">
                @foreach (session('alternatives', []) as $alt)
                    <button type="button" class="btn btn-outline-primary btn-sm alt-slot-btn"
                            data-date="{{ $alt['date'] }}" data-time="{{ $alt['time'] }}">
                        {{ $alt['label'] }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    <form action="{{ route('appointment.store') }}" method="POST" id="bookingForm">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="row">
            <!-- ============ CỘT TRÁI: THÔNG TIN ============ -->
            <div class="col-md-7">

                <!-- BƯỚC 1: TÌM PHÒNG KHÁM GẦN BẠN -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h3>Bước 1: Tìm phòng khám gần bạn</h3>
                        <div class="row">
                            <div class="col-md-8 mb-2">
                                <label>Khu vực / Quận của bạn</label>
                                <select id="user_district" class="form-control">
                                    <option value="">-- Chọn khu vực để tìm phòng khám gần nhất --</option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district }}">{{ $district }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-2 d-flex align-items-end">
                                <button type="button" id="findNearbyBtn" class="btn btn-outline-success w-100">
                                    <i class="fa fa-map-marker-alt"></i> Tìm gần tôi
                                </button>
                            </div>
                        </div>
                        <div id="nearbyClinicsResult" class="mt-2" style="display:none;">
                            <p class="mb-1"><strong>Phòng khám gần bạn (sắp xếp theo khoảng cách):</strong></p>
                            <div id="nearbyClinicsList" class="list-group"></div>
                        </div>
                    </div>
                </div>

                <!-- BƯỚC 2: CHỌN CHUYÊN KHOA THEO TRIỆU CHỨNG -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h3>Bước 2: Triệu chứng & Chuyên khoa</h3>
                        <div class="mb-3">
                            <label>Triệu chứng của bạn</label>
                            <textarea name="symptoms" id="symptoms" class="form-control" rows="2"
                                placeholder="VD: đau đầu, sốt, ho, đau bụng...">{{ old('symptoms') }}</textarea>
                            <button type="button" id="suggestSpecialtyBtn" class="btn btn-sm btn-outline-info mt-2">
                                <i class="fa fa-lightbulb"></i> Gợi ý chuyên khoa phù hợp
                            </button>
                            <small id="specialtySuggestionText" class="text-success d-block mt-1"></small>
                        </div>
                        <div class="mb-3">
                            <label>Chuyên khoa</label>
                            <select id="specialty_id" class="form-control">
                                <option value="">-- Tất cả chuyên khoa --</option>
                                @foreach ($specialties as $specialty)
                                    <option value="{{ $specialty->id }}">{{ $specialty->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- BƯỚC 3: ĐỀ XUẤT CÁ NHÂN HÓA -->
                @if (!empty($recommendedDoctors))
                <div class="card mb-3 border-primary">
                    <div class="card-body">
                        <h3><i class="fa fa-star text-warning"></i> Gợi ý dành riêng cho bạn</h3>
                        <p class="text-muted">Dựa trên lịch sử đặt lịch và chuyên khoa bạn đã khám gần đây</p>
                        <div class="row">
                            @foreach ($recommendedDoctors as $rec)
                                <div class="col-md-6 mb-2">
                                    <div class="border rounded p-2 recommend-card" data-doctor-id="{{ $rec->id }}">
                                        <strong>{{ $rec->name }}</strong> ({{ $rec->degree }})<br>
                                        <small class="text-muted">{{ $rec->specialty->name ?? '' }} - {{ $rec->clinic_name }}</small><br>
                                        <small>{{ number_format($rec->consultation_fee) }} VNĐ - <i class="fa fa-star text-warning"></i> {{ $rec->rating }}</small>
                                        <button type="button" class="btn btn-sm btn-primary mt-1 float-end select-doctor-btn"
                                                data-id="{{ $rec->id }}">Chọn</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- BƯỚC 4: CHỌN BÁC SĨ -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h3>Bước 3: Chọn bác sĩ</h3>
                        <div class="mb-2">
                            <label>Sắp xếp theo</label>
                            <select id="sortDoctors" class="form-control">
                                <option value="default">Mặc định</option>
                                <option value="fee_asc">Phí khám: thấp đến cao</option>
                                <option value="fee_desc">Phí khám: cao đến thấp</option>
                                <option value="rating">Đánh giá cao nhất</option>
                                <option value="distance">Khoảng cách gần nhất</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Bác sĩ *</label>
                            <select id="doctor_id" name="doctor_id" class="form-control" required>
                                <option value="">-- Chọn bác sĩ --</option>
                                @foreach ($doctors as $doctor)
                                    <option value="{{ $doctor->id }}"
                                            data-specialty="{{ $doctor->specialty_id }}"
                                            data-fee="{{ $doctor->consultation_fee }}"
                                            data-rating="{{ $doctor->rating }}"
                                            data-clinic="{{ $doctor->clinic_name }}"
                                            data-address="{{ $doctor->clinic_address }}"
                                            data-district="{{ $doctor->clinic_district }}"
                                            data-lat="{{ $doctor->latitude }}"
                                            data-lng="{{ $doctor->longitude }}"
                                            {{ old('doctor_id', $selectedDoctor->id ?? '') == $doctor->id ? 'selected' : '' }}>
                                        {{ $doctor->name }} ({{ $doctor->specialty->name }}) - {{ number_format($doctor->consultation_fee) }} VNĐ
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Thông tin chi tiết bác sĩ đã chọn -->
                        <div id="doctorInfoBox" class="border rounded p-3" style="display:none; background:#f8f9fa;">
                            <div class="row">
                                <div class="col-md-8">
                                    <p class="mb-1"><strong>Phòng khám:</strong> <span id="info_clinic"></span></p>
                                    <p class="mb-1"><strong>Địa chỉ:</strong> <span id="info_address"></span></p>
                                    <p class="mb-1"><strong>Đánh giá:</strong> <span id="info_rating"></span> / 5</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <p class="mb-1"><strong>Phí khám:</strong></p>
                                    <h5 class="text-danger" id="info_fee"></h5>
                                    <p class="mb-1" id="info_distance_wrap" style="display:none;">
                                        <i class="fa fa-route"></i> <span id="info_distance"></span> km
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BƯỚC 5: THỜI GIAN MONG MUỐN -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h3>Bước 4: Chọn thời gian mong muốn</h3>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Ngày khám *</label>
                                <input type="date" id="appointment_date" name="appointment_date"
                                       class="form-control" min="{{ date('Y-m-d') }}"
                                       value="{{ old('appointment_date', $selectedDate ?? '') }}" required>
                                <small id="working_day_note" class="text-muted"></small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Giờ khám mong muốn *</label>
                                <select id="appointment_time" name="appointment_time" class="form-control time-slot-select" required>
                                    <option value="">-- Chọn bác sĩ và ngày trước --</option>
                                </select>
                                <small id="loading_slots" style="display:none; color:#888;">Đang tải khung giờ...</small>
                            </div>
                        </div>
                        <small class="text-muted">
                            <i class="fa fa-info-circle"></i> Hệ thống sẽ tự kiểm tra xung đột lịch hẹn.
                            Nếu giờ bạn chọn đã có người đặt, chúng tôi sẽ gợi ý thời gian gần nhất phù hợp.
                        </small>
                    </div>
                </div>

                <!-- BƯỚC 6: THÔNG TIN LIÊN LẠC -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h3>Bước 5: Thông tin liên lạc</h3>
                        <div class="mb-3">
                            <label>Họ tên *</label>
                            <input type="text" name="fullname" class="form-control" value="{{ old('fullname') }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Email * (lịch hẹn sẽ được gửi nhắc nhở qua email này)</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Số điện thoại *</label>
                            <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Ghi chú</label>
                            <textarea name="note" class="form-control" rows="2">{{ old('note') }}</textarea>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ============ CỘT PHẢI: TÓM TẮT & THANH TOÁN ============ -->
            <div class="col-md-5">
                <div class="card sticky-top" style="top:20px;">
                    <div class="card-body">
                        <h3>Tóm tắt lịch hẹn</h3>
                        <div id="summaryBox">
                            <p class="text-muted">Vui lòng chọn bác sĩ và thời gian khám</p>
                        </div>
                        <table class="table table-sm" id="summaryTable" style="display:none;">
                            <tr>
                                <td>Bác sĩ</td>
                                <td class="text-end" id="sum_doctor">-</td>
                            </tr>
                            <tr>
                                <td>Phòng khám</td>
                                <td class="text-end" id="sum_clinic">-</td>
                            </tr>
                            <tr>
                                <td>Ngày</td>
                                <td class="text-end" id="sum_date">-</td>
                            </tr>
                            <tr>
                                <td>Giờ</td>
                                <td class="text-end" id="sum_time">-</td>
                            </tr>
                            <tr>
                                <td><strong>Phí khám</strong></td>
                                <td class="text-end"><strong class="text-danger" id="sum_fee">-</strong></td>
                            </tr>
                        </table>

                        <hr>
                        <h5>Phương thức thanh toán</h5>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="pay_cash" value="cash" checked>
                            <label class="form-check-label" for="pay_cash">Thanh toán tại phòng khám</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="pay_momo" value="momo">
                            <label class="form-check-label" for="pay_momo">Thanh toán qua MoMo</label>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" id="agree" name="agree" class="form-check-input" required>
                            <label class="form-check-label" for="agree">
                                Tôi đồng ý với <a href="#" target="_blank">điều khoản dịch vụ</a> và chính sách hủy lịch
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                            <i class="fa fa-calendar-check"></i> Xác nhận đặt lịch
                        </button>
                        <p class="text-muted text-center mt-2 mb-0">
                            <small>Bạn sẽ nhận email nhắc nhở trước lịch hẹn</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

@include('clients.blocks.footer')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const specialtySelect = document.getElementById('specialty_id');
    const doctorSelect = document.getElementById('doctor_id');
    const dateInput = document.getElementById('appointment_date');
    const timeSelect = document.getElementById('appointment_time');
    const loadingSpan = document.getElementById('loading_slots');
    const districtSelect = document.getElementById('user_district');
    const sortSelect = document.getElementById('sortDoctors');

    // ===== Lưu danh sách bác sĩ gốc =====
    let originalDoctors = [];
    if (doctorSelect) {
        Array.from(doctorSelect.options).forEach(opt => {
            if (opt.value) {
                originalDoctors.push({
                    value: opt.value,
                    text: opt.text,
                    specialty: opt.getAttribute('data-specialty'),
                    fee: parseInt(opt.getAttribute('data-fee')) || 0,
                    rating: parseFloat(opt.getAttribute('data-rating')) || 0,
                    clinic: opt.getAttribute('data-clinic'),
                    address: opt.getAttribute('data-address'),
                    district: opt.getAttribute('data-district'),
                    lat: parseFloat(opt.getAttribute('data-lat')) || null,
                    lng: parseFloat(opt.getAttribute('data-lng')) || null,
                });
            }
        });
    }

    // ===== Toạ độ mô phỏng theo quận (để tính khoảng cách) =====
    const districtCoords = {
        "Hoàn Kiếm": {lat: 21.0285, lng: 105.8542},
        "Ba Đình": {lat: 21.0333, lng: 105.8142},
        "Đống Đa": {lat: 21.0151, lng: 105.8281},
        "Hai Bà Trưng": {lat: 21.0073, lng: 105.8551},
        "Cầu Giấy": {lat: 21.0312, lng: 105.7926},
        "Thanh Xuân": {lat: 20.9967, lng: 105.8055},
        "Hà Đông": {lat: 20.9540, lng: 105.7768},
        "Long Biên": {lat: 21.0381, lng: 105.8980},
        "Tây Hồ": {lat: 21.0723, lng: 105.8231},
        "Hoàng Mai": {lat: 20.9760, lng: 105.8460},
    };

    function haversineDistance(lat1, lng1, lat2, lng2) {
        const R = 6371; // km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLng = (lng2 - lng1) * Math.PI / 180;
        const a = Math.sin(dLat/2) ** 2 +
                  Math.cos(lat1 * Math.PI/180) * Math.cos(lat2 * Math.PI/180) *
                  Math.sin(dLng/2) ** 2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }

    // ===== Bước 1: Tìm phòng khám gần bạn theo khu vực =====
    if (districtSelect) {
        districtSelect.addEventListener('change', function() {
            const district = this.value;
            const resultBox = document.getElementById('nearbyClinicsResult');
            const listBox = document.getElementById('nearbyClinicsList');
            if (!district || !districtCoords[district]) {
                resultBox.style.display = 'none';
                return;
            }
            const userCoord = districtCoords[district];

            const withDistance = originalDoctors.map(doc => {
                let distance = null;
                if (doc.lat && doc.lng) {
                    distance = haversineDistance(userCoord.lat, userCoord.lng, doc.lat, doc.lng);
                } else if (districtCoords[doc.district]) {
                    const c = districtCoords[doc.district];
                    distance = haversineDistance(userCoord.lat, userCoord.lng, c.lat, c.lng);
                }
                return {...doc, distance: distance};
            }).filter(d => d.distance !== null)
              .sort((a,b) => a.distance - b.distance)
              .slice(0, 5);

            if (withDistance.length === 0) {
                listBox.innerHTML = '<p class="text-muted">Không tìm thấy phòng khám phù hợp.</p>';
            } else {
                listBox.innerHTML = withDistance.map(d => `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${d.clinic || 'Phòng khám'}</strong><br>
                            <small class="text-muted">${d.address || ''}</small><br>
                            <small>${d.text} - ${d.fee.toLocaleString()} VNĐ</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success">${d.distance.toFixed(1)} km</span><br>
                            <button type="button" class="btn btn-sm btn-primary mt-1 select-doctor-from-list"
                                    data-id="${d.value}" data-distance="${d.distance.toFixed(1)}">Chọn</button>
                        </div>
                    </div>
                `).join('');
            }
            resultBox.style.display = 'block';
        });
    }

    // Click chọn bác sĩ từ danh sách gần bạn
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('select-doctor-from-list') || e.target.classList.contains('select-doctor-btn')) {
            const id = e.target.getAttribute('data-id');
            doctorSelect.value = id;
            doctorSelect.dispatchEvent(new Event('change', { bubbles: true }));
            // Lưu khoảng cách nếu có
            const distance = e.target.getAttribute('data-distance');
            if (distance) {
                doctorSelect.setAttribute('data-current-distance', distance);
            }
            document.getElementById('doctorInfoBox').scrollIntoView({behavior: 'smooth', block: 'center'});
        }
    });

    // ===== "Tìm gần tôi" (Geolocation) =====
    const findNearbyBtn = document.getElementById('findNearbyBtn');
    if (findNearbyBtn) {
        findNearbyBtn.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Trình duyệt không hỗ trợ định vị. Vui lòng chọn khu vực thủ công.');
                return;
            }
            findNearbyBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang định vị...';
            navigator.geolocation.getCurrentPosition(function(pos) {
                const userLat = pos.coords.latitude;
                const userLng = pos.coords.longitude;

                const withDistance = originalDoctors.map(doc => {
                    let distance = null;
                    if (doc.lat && doc.lng) {
                        distance = haversineDistance(userLat, userLng, doc.lat, doc.lng);
                    }
                    return {...doc, distance: distance};
                }).filter(d => d.distance !== null)
                  .sort((a,b) => a.distance - b.distance)
                  .slice(0, 5);

                const resultBox = document.getElementById('nearbyClinicsResult');
                const listBox = document.getElementById('nearbyClinicsList');
                if (withDistance.length === 0) {
                    listBox.innerHTML = '<p class="text-muted">Không tìm thấy phòng khám có dữ liệu vị trí.</p>';
                } else {
                    listBox.innerHTML = withDistance.map(d => `
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${d.clinic || 'Phòng khám'}</strong><br>
                                <small class="text-muted">${d.address || ''}</small><br>
                                <small>${d.text} - ${d.fee.toLocaleString()} VNĐ</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success">${d.distance.toFixed(1)} km</span><br>
                                <button type="button" class="btn btn-sm btn-primary mt-1 select-doctor-from-list"
                                        data-id="${d.value}" data-distance="${d.distance.toFixed(1)}">Chọn</button>
                            </div>
                        </div>
                    `).join('');
                }
                resultBox.style.display = 'block';
                findNearbyBtn.innerHTML = '<i class="fa fa-map-marker-alt"></i> Tìm gần tôi';
            }, function(err) {
                alert('Không lấy được vị trí của bạn. Vui lòng chọn khu vực thủ công.');
                findNearbyBtn.innerHTML = '<i class="fa fa-map-marker-alt"></i> Tìm gần tôi';
            });
        });
    }

    // ===== Bước 2: Gợi ý chuyên khoa theo triệu chứng =====
    const symptomMap = {
        'tim mạch': ['tim', 'huyết áp', 'ngực', 'hồi hộp', 'tức ngực'],
        'thần kinh': ['đau đầu', 'chóng mặt', 'mất ngủ', 'tê', 'co giật'],
        'tiêu hóa': ['đau bụng', 'tiêu chảy', 'táo bón', 'buồn nôn', 'ợ chua', 'dạ dày'],
        'tai mũi họng': ['ho', 'sổ mũi', 'đau họng', 'ngạt mũi', 'viêm họng'],
        'da liễu': ['da', 'mụn', 'ngứa', 'nổi mẩn', 'dị ứng da'],
        'nhi': ['trẻ em', 'sốt trẻ', 'bé'],
        'mắt': ['mắt', 'nhìn mờ', 'đau mắt', 'ngứa mắt'],
        'xương khớp': ['khớp', 'đau lưng', 'đau khớp', 'gãy xương', 'đau cơ'],
        'sản phụ khoa': ['thai', 'kinh nguyệt', 'phụ khoa', 'mang thai'],
        'nội tổng quát': ['sốt', 'mệt mỏi', 'cảm cúm'],
    };

    const suggestBtn = document.getElementById('suggestSpecialtyBtn');
    if (suggestBtn) {
        suggestBtn.addEventListener('click', function() {
            const text = document.getElementById('symptoms').value.toLowerCase().trim();
            const noteEl = document.getElementById('specialtySuggestionText');
            if (!text) {
                noteEl.textContent = 'Vui lòng nhập triệu chứng trước.';
                noteEl.className = 'text-danger d-block mt-1';
                return;
            }

            let matched = [];
            for (const [key, keywords] of Object.entries(symptomMap)) {
                for (const kw of keywords) {
                    if (text.includes(kw)) {
                        matched.push(key);
                        break;
                    }
                }
            }
            matched = [...new Set(matched)];

            if (matched.length > 0) {
                // Tìm option chuyên khoa khớp tên (không phân biệt hoa thường, chứa từ khóa)
                let found = false;
                for (let opt of specialtySelect.options) {
                    const optName = opt.text.toLowerCase();
                    for (const m of matched) {
                        if (optName.includes(m)) {
                            specialtySelect.value = opt.value;
                            specialtySelect.dispatchEvent(new Event('change', { bubbles: true }));
                            found = true;
                            break;
                        }
                    }
                    if (found) break;
                }
                noteEl.textContent = found
                    ? `Gợi ý: Chuyên khoa "${specialtySelect.options[specialtySelect.selectedIndex].text}" phù hợp với triệu chứng của bạn.`
                    : `Gợi ý chuyên khoa: ${matched.join(', ')} (chưa tìm thấy trong danh sách hiện tại).`;
                noteEl.className = 'text-success d-block mt-1';
            } else {
                noteEl.textContent = 'Không xác định được chuyên khoa phù hợp, vui lòng chọn thủ công hoặc liên hệ tư vấn.';
                noteEl.className = 'text-warning d-block mt-1';
            }
        });
    }

    // ===== Lọc bác sĩ theo chuyên khoa =====
    function filterDoctorsBySpecialty() {
        if (!doctorSelect || !specialtySelect) return;
        const selectedSpecialty = specialtySelect.value;
        const currentValue = doctorSelect.value;

        renderDoctorOptions(selectedSpecialty);

        if (currentValue && Array.from(doctorSelect.options).some(o => o.value == currentValue)) {
            doctorSelect.value = currentValue;
        } else {
            doctorSelect.value = '';
            updateDoctorInfo();
        }
    }

    // ===== Render danh sách bác sĩ (có lọc + sắp xếp) =====
    function renderDoctorOptions(specialtyFilter = '') {
        const sortBy = sortSelect ? sortSelect.value : 'default';
        let list = originalDoctors.filter(doc => !specialtyFilter || doc.specialty === specialtyFilter);

        if (sortBy === 'fee_asc') list = [...list].sort((a,b) => a.fee - b.fee);
        else if (sortBy === 'fee_desc') list = [...list].sort((a,b) => b.fee - a.fee);
        else if (sortBy === 'rating') list = [...list].sort((a,b) => b.rating - a.rating);
        else if (sortBy === 'distance') {
            const currentDistrict = districtSelect ? districtSelect.value : '';
            if (currentDistrict && districtCoords[currentDistrict]) {
                const userCoord = districtCoords[currentDistrict];
                list = [...list].map(doc => {
                    let distance = 9999;
                    if (doc.lat && doc.lng) distance = haversineDistance(userCoord.lat, userCoord.lng, doc.lat, doc.lng);
                    else if (districtCoords[doc.district]) {
                        const c = districtCoords[doc.district];
                        distance = haversineDistance(userCoord.lat, userCoord.lng, c.lat, c.lng);
                    }
                    return {...doc, _distance: distance};
                }).sort((a,b) => a._distance - b._distance);
            }
        }

        doctorSelect.innerHTML = '<option value="">-- Chọn bác sĩ --</option>';
        list.forEach(doc => {
            const opt = document.createElement('option');
            opt.value = doc.value;
            opt.text = doc.text;
            opt.setAttribute('data-specialty', doc.specialty);
            opt.setAttribute('data-fee', doc.fee);
            opt.setAttribute('data-rating', doc.rating);
            opt.setAttribute('data-clinic', doc.clinic || '');
            opt.setAttribute('data-address', doc.address || '');
            opt.setAttribute('data-district', doc.district || '');
            opt.setAttribute('data-lat', doc.lat || '');
            opt.setAttribute('data-lng', doc.lng || '');
            doctorSelect.appendChild(opt);
        });
    }

    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const currentValue = doctorSelect.value;
            renderDoctorOptions(specialtySelect ? specialtySelect.value : '');
            if (currentValue && Array.from(doctorSelect.options).some(o => o.value == currentValue)) {
                doctorSelect.value = currentValue;
            }
        });
    }

    // ===== Hiển thị thông tin chi tiết bác sĩ đã chọn =====
    function updateDoctorInfo() {
        const infoBox = document.getElementById('doctorInfoBox');
        if (!doctorSelect.value) {
            infoBox.style.display = 'none';
            updateSummary();
            return;
        }
        const opt = doctorSelect.options[doctorSelect.selectedIndex];
        document.getElementById('info_clinic').textContent = opt.getAttribute('data-clinic') || '-';
        document.getElementById('info_address').textContent = opt.getAttribute('data-address') || '-';
        document.getElementById('info_rating').textContent = opt.getAttribute('data-rating') || '0';
        document.getElementById('info_fee').textContent = (parseInt(opt.getAttribute('data-fee')) || 0).toLocaleString() + ' VNĐ';

        const distance = doctorSelect.getAttribute('data-current-distance');
        const distWrap = document.getElementById('info_distance_wrap');
        if (distance) {
            document.getElementById('info_distance').textContent = distance;
            distWrap.style.display = 'block';
        } else {
            distWrap.style.display = 'none';
        }

        infoBox.style.display = 'block';
        updateSummary();
    }

    // ===== Cập nhật tóm tắt bên phải =====
    function updateSummary() {
        const summaryBox = document.getElementById('summaryBox');
        const summaryTable = document.getElementById('summaryTable');

        if (!doctorSelect.value) {
            summaryBox.style.display = 'block';
            summaryTable.style.display = 'none';
            return;
        }

        const opt = doctorSelect.options[doctorSelect.selectedIndex];
        document.getElementById('sum_doctor').textContent = opt.text.split(' (')[0];
        document.getElementById('sum_clinic').textContent = opt.getAttribute('data-clinic') || '-';
        document.getElementById('sum_date').textContent = dateInput.value || '-';
        document.getElementById('sum_time').textContent = timeSelect.value || '-';
        document.getElementById('sum_fee').textContent = (parseInt(opt.getAttribute('data-fee')) || 0).toLocaleString() + ' VNĐ';

        summaryBox.style.display = 'none';
        summaryTable.style.display = 'table';
    }

    // ===== Refresh UI của select sau khi đổi innerHTML =====
    // Một số theme dùng plugin "nice-select" / "select2" sẽ wrap thẻ <select> gốc
    // thành 1 widget riêng và ẨN select gốc đi. Khi ta đổi innerHTML của select gốc,
    // widget hiển thị không tự cập nhật theo => phải refresh lại widget đó.
    function refreshTimeSelectUI() {
        if (typeof $ === 'undefined' || !$.fn) return; // jQuery chưa load thì bỏ qua

        const $select = $(timeSelect);

        // Trường hợp dùng plugin "nice-select" (jquery.nice-select.min.js)
        if ($select.next('.nice-select').length) {
            if (typeof $select.niceSelect === 'function') {
                $select.niceSelect('update');
            } else {
                // Fallback: tự destroy & re-init lại nice-select
                $select.next('.nice-select').remove();
                $select.show();
                if (typeof $select.niceSelect === 'function') {
                    $select.niceSelect();
                }
            }
        }

        // Trường hợp dùng plugin "select2"
        if ($select.hasClass('select2-hidden-accessible') && typeof $select.trigger === 'function') {
            $select.trigger('change.select2');
        }
    }

    // ===== Tải khung giờ trống (gọi API kiểm tra xung đột) =====
    async function loadSlots() {
        const doctorId = doctorSelect ? doctorSelect.value : null;
        const date = dateInput ? dateInput.value : null;

        if (!doctorId || !date) {
            timeSelect.innerHTML = '<option value="">-- Chọn bác sĩ và ngày trước --</option>';
            refreshTimeSelectUI();
            updateSummary();
            return;
        }

        if (loadingSpan) loadingSpan.style.display = 'inline';

        let token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!token) {
            const tokenInput = document.querySelector('input[name="_token"]');
            if (tokenInput) token = tokenInput.value;
        }
        if (!token) {
            if (loadingSpan) loadingSpan.style.display = 'none';
            timeSelect.innerHTML = '<option value="">Lỗi bảo mật (CSRF)</option>';
            refreshTimeSelectUI();
            return;
        }

        try {
            const response = await fetch('{{ route("appointment.check-slots") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ doctor_id: doctorId, date: date })
            });
            const data = await response.json();
            if (loadingSpan) loadingSpan.style.display = 'none';

            if (data.available === false) {
                timeSelect.innerHTML = '<option value="">' + (data.message || 'Bác sĩ không làm việc ngày này') + '</option>';
                refreshTimeSelectUI();
                document.getElementById('working_day_note').textContent = data.message || '';
                updateSummary();
                return;
            }

            document.getElementById('working_day_note').textContent = '';
            const slots = data.slots || [];

            if (slots.length > 0) {
                const optionsHtml = '<option value="">-- Chọn giờ khám mong muốn --</option>' +
                    slots.map(slot => {
                        const isAvail = slot.available !== false;
                        return `<option value="${slot.time}" ${!isAvail ? 'disabled' : ''}>${slot.time}${!isAvail ? ' (đã đặt)' : ''}</option>`;
                    }).join('');
                timeSelect.innerHTML = optionsHtml;
            } else {
                timeSelect.innerHTML = '<option value="">Không có khung giờ trống</option>';
            }
            refreshTimeSelectUI();
            updateSummary();
        } catch (error) {
            if (loadingSpan) loadingSpan.style.display = 'none';
            timeSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
            refreshTimeSelectUI();
        }
    }

    // ===== Gắn sự kiện =====
    if (specialtySelect) specialtySelect.addEventListener('change', filterDoctorsBySpecialty);
    if (doctorSelect) {
        doctorSelect.addEventListener('change', function() {
            doctorSelect.removeAttribute('data-current-distance');
            updateDoctorInfo();
            loadSlots();
        });
    }
    if (dateInput) dateInput.addEventListener('change', loadSlots);
    if (timeSelect) timeSelect.addEventListener('change', updateSummary);

    // ===== Xử lý click chọn thời gian thay thế (khi có xung đột) =====
    document.querySelectorAll('.alt-slot-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const date = this.getAttribute('data-date');
            const time = this.getAttribute('data-time');
            dateInput.value = date;
            await loadSlots();
            // Đợi load slot xong rồi set giá trị giờ
            setTimeout(() => {
                if (Array.from(timeSelect.options).some(o => o.value === time)) {
                    timeSelect.value = time;
                    timeSelect.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }, 300);
        });
    });

    // ===== Khởi tạo ban đầu =====
    if (specialtySelect) filterDoctorsBySpecialty();
    if (doctorSelect && doctorSelect.value) {
        updateDoctorInfo();
        if (dateInput && dateInput.value) loadSlots();
    }
});
</script>

<script>
// Pre-select bác sĩ từ query string (chạy sau tất cả script khác)
window.addEventListener('load', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const doctorIdFromUrl = urlParams.get('doctor_id');

    if (doctorIdFromUrl) {
        const doctorSelect = document.getElementById('doctor_id');
        if (doctorSelect) {
            doctorSelect.value = doctorIdFromUrl;

            // Cập nhật nice-select UI nếu đang dùng
            if (typeof $ !== 'undefined') {
                const $select = $(doctorSelect);

                // Nếu dùng nice-select
                if ($select.next('.nice-select').length) {
                    const label = $select.find('option:selected').text();
                    $select.next('.nice-select').find('.current').text(label);
                    $select.next('.nice-select').find('.option').each(function() {
                        if ($(this).attr('data-value') == doctorIdFromUrl) {
                            $(this).addClass('selected focus');
                        } else {
                            $(this).removeClass('selected focus');
                        }
                    });
                }
            }

            // Trigger change để load thông tin bác sĩ và khung giờ
            doctorSelect.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }
});
</script>