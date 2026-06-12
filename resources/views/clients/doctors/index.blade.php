@include('clients.blocks.header')
@include('clients.blocks.banner')

<section class="py-100">
    <div class="container">
        <div class="row">
            @forelse($doctors as $doctor)
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="destination-item">
                        <div class="image">
                            <img src="{{ asset('uploads/doctors/' . $doctor->avatar) }}"
                                alt="{{ $doctor->doctor_name }}">
                        </div>
                        <div class="content">
                            <span class="location">
                                <i class="fas fa-stethoscope"></i>
                                {{ $doctor->specialty->name ?? 'Chưa cập nhật' }}
                            </span>
                            <h5>
                                <a href="{{ route('doctor.detail', $doctor->id) }}">
                                    {{ $doctor->name }}
                                </a>
                            </h5>
                            <span>{{ $doctor->clinic_name }}</span>
                        </div>
                        <div class="destination-footer">
                            <span class="price">
                                {{ number_format($doctor->consultation_fee) }} VNĐ
                            </span>
                            <a href="{{ route('doctor.detail', $doctor->id) }}" class="read-more">
                                Đặt lịch
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="alert alert-info">Không tìm thấy bác sĩ nào.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

@include('clients.blocks.footer')