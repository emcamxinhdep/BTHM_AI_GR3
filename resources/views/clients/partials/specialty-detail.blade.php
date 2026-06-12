@include('clients.blocks.header')
@include('clients.blocks.banner')

<section class="tour-grid-page py-100 rel z-2">
    <div class="container">
        <div class="row justify-content-center mb-50">
            <div class="col-lg-8 text-center">
                <h2>{{ $specialty->name }}</h2>
                <p>{{ $specialty->description ?? '' }}</p>
            </div>
        </div>
        <div class="row">
            @forelse($doctors as $doctor)
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="destination-item" data-aos="fade-up"
                        data-aos-duration="1500" data-aos-offset="50">
                        <div class="image">
                            <img src="{{ asset('uploads/doctors/' . $doctor->avatar) }}"
                                alt="{{ $doctor->name }}">
                        </div>
                        <div class="content">
                            <span class="location">
                                <i class="fas fa-stethoscope"></i>
                                {{ $specialty->name }}
                            </span>
                            <h5>
                                <a href="{{ route('doctor.detail', $doctor->id) }}">
                                    BS. {{ $doctor->name }}
                                </a>
                            </h5>
                            <span>{{ $doctor->hospital }}</span>
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
                    <p class="alert alert-info">Chưa có bác sĩ nào trong chuyên khoa này.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

@include('clients.blocks.footer')