@include('clients.blocks.header')
@include('clients.blocks.banner')

<section class="tour-grid-page py-100 rel z-2">
    <div class="container">
        <div class="row justify-content-center mb-50">
            <div class="col-lg-8 text-center">
                <h2>Chuyên khoa</h2>
                <p>Tìm kiếm bác sĩ theo chuyên khoa phù hợp với nhu cầu của bạn</p>
            </div>
        </div>
        <div class="row">
            @forelse($specialties as $specialty)
                <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                    <div class="destination-item text-center" data-aos="fade-up"
                        data-aos-duration="1500" data-aos-offset="50">
                        <div class="content p-4">
                            <h5>
                                <a href="{{ route('specialty.detail', $specialty->id) }}">
                                    {{ $specialty->name }}
                                </a>
                            </h5>
                            <span>{{ $specialty->doctors_count ?? 0 }} bác sĩ</span>
                        </div>
                        <div class="destination-footer">
                            <a href="{{ route('specialty.detail', $specialty->id) }}" class="read-more">
                                Xem bác sĩ
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="alert alert-info">Chưa có chuyên khoa nào.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

@include('clients.blocks.footer')