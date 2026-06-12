@include('clients.blocks.header')
@include('clients.blocks.banner')

<section class="py-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="card mb-4">
                    <img src="{{ asset('uploads/doctors/' . $doctor->avatar) }}"
                        class="card-img-top" alt="{{ $doctor->name }}">
                    <div class="card-body text-center">
                        <h4>{{ $doctor->name }}</h4>
                        <p class="text-muted">{{ $doctor->specialty->name ?? '' }}</p>
                        <a href="{{ route('appointment.create', ['doctor_id' => $doctor->id]) }}"
                            class="theme-btn style-two w-100">
                            <span data-hover="Đặt lịch khám">Đặt lịch khám</span>
                            <i class="fal fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Thông tin bác sĩ</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><i class="fas fa-hospital me-2"></i>Bệnh viện</td>
                                <td>{{ $doctor->clinic_name }}</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-clock me-2"></i>Kinh nghiệm</td>
                                <td>{{ $doctor->experience_years }} năm</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-money-bill me-2"></i>Phí khám</td>
                                <td>{{ number_format($doctor->consultation_fee) }} VNĐ</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-star me-2 text-warning"></i>Đánh giá</td>
                                <td>{{ $doctor->rating ?? 0 }} / 5</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Giới thiệu</h5>
                        <p>{{ $doctor->description ?? 'Chưa có thông tin.' }}</p>
                    </div>
                </div>

                {{-- Lịch làm việc --}}
                @php
                    $validWorkingHours = is_array($workingHours) && count($workingHours) > 0 && isset($workingHours[0]['day']);
                @endphp

                @if($validWorkingHours)
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Lịch làm việc</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr><th>Thứ</th><th>Giờ bắt đầu</th><th>Giờ kết thúc</th></tr>
                            </thead>
                            <tbody>
                                @foreach($workingHours as $schedule)
                                <tr>
                                    <td>{{ ucfirst($schedule['day'] ?? '') }}</td>
                                    <td>{{ $schedule['start'] ?? '' }}</td>
                                    <td>{{ $schedule['end'] ?? '' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @elseif(is_string($workingHours) && !empty($workingHours))
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="mb-3">Lịch làm việc</h5>
                            <p>{{ $workingHours }}</p>
                        </div>
                    </div>
                @endif

                {{-- Đánh giá --}}
                @if($doctor->reviews && $doctor->reviews->count() > 0)
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">Đánh giá từ bệnh nhân</h5>
                        @foreach($doctor->reviews as $review)
                        <div class="border-bottom pb-3 mb-3">
                            <strong>{{ $review->patient->name ?? 'Ẩn danh' }}</strong>
                            <span class="text-warning ms-2">
                                @for($i = 0; $i < $review->rating; $i++)
                                    <i class="fas fa-star"></i>
                                @endfor
                            </span>
                            <p class="mb-0 mt-1">{{ $review->comment }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

@include('clients.blocks.footer')