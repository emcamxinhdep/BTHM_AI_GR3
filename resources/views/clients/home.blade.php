@include('clients.blocks.header_home')
@include('clients.blocks.banner_home')

<!--Form Back Drop-->
<div class="form-back-drop"></div>

<!-- Destinations Area start -->
<section class="destinations-area bgc-black pt-100 pb-70 rel z-1">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="section-title text-white text-center counter-text-wrap mb-70"
                    data-aos="fade-up" data-aos-duration="1500" data-aos-offset="50">
                    <h2>Kết nối với bác sĩ uy tín trên DoctorCam</h2>
                    <p>Đặt lịch khám nhanh chóng, tư vấn trực tuyến và chăm sóc sức khỏe mọi lúc mọi nơi.</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            @foreach ($doctors as $doctor)
            <div class="col-xxl-3 col-xl-4 col-md-6">
                <div class="destination-item">
                    <div class="image">
                        <!-- <img src="{{ asset('uploads/doctors/' . $doctor->avatar) }}"
                            alt="{{ $doctor->name }}"> -->
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
            @endforeach
        </div>
    </div>
</section>
<!-- Destinations Area end -->


<!-- About Us Area start -->
<section class="about-us-area py-100 rpb-90 rel z-1">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-5 col-lg-6">
                <div class="about-us-content rmb-55" data-aos="fade-left"
                    data-aos-duration="1500" data-aos-offset="50">
                    <div class="section-title mb-25">
                        <h2>Chăm sóc sức khỏe dễ dàng cùng DoctorCam</h2>
                        <p>DoctorCam giúp người bệnh tìm kiếm bác sĩ phù hợp,
                            đặt lịch khám trực tuyến, theo dõi lịch sử khám bệnh
                            và nhận tư vấn y tế nhanh chóng.</p>
                    </div>
                    <div class="counter-item">
                        <span class="count-text">200+</span>
                        <span class="counter-title">Bác sĩ chuyên khoa</span>
                    </div>
                    <div class="counter-item">
                        <span class="count-text">10K+</span>
                        <span class="counter-title">Bệnh nhân hài lòng</span>
                    </div>
                    <a href="{{ route('doctors') }}" class="theme-btn mt-10 style-two">
                        <span data-hover="Xem danh sách bác sĩ">Xem danh sách bác sĩ</span>
                        <i class="fal fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-xl-7 col-lg-6" data-aos="fade-right"
                data-aos-duration="1500" data-aos-offset="50">
                <div class="about-us-image">
                    <div class="shape"><img src="{{ asset('clients/assets/images/about/shape1.png') }}" alt="Shape"></div>
                    <div class="shape"><img src="{{ asset('clients/assets/images/about/shape2.png') }}" alt="Shape"></div>
                    <div class="shape"><img src="{{ asset('clients/assets/images/about/shape3.png') }}" alt="Shape"></div>
                    <div class="shape"><img src="{{ asset('clients/assets/images/about/shape4.png') }}" alt="Shape"></div>
                    <div class="shape"><img src="{{ asset('clients/assets/images/about/shape5.png') }}" alt="Shape"></div>
                    <div class="shape"><img src="{{ asset('clients/assets/images/about/shape6.png') }}" alt="Shape"></div>
                    <div class="shape"><img src="{{ asset('clients/assets/images/about/shape7.png') }}" alt="Shape"></div>
                    <img src="{{ asset('clients/assets/images/about/about.png') }}" alt="About">
                </div>
            </div>
        </div>
    </div>
</section>
<!-- About Us Area end -->


<!-- Popular Destinations Area start -->
<section class="popular-destinations-area rel z-1">
    <div class="container-fluid">
        <div class="popular-destinations-wrap br-20 bgc-lighter pt-100 pb-70">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="section-title text-center counter-text-wrap mb-70"
                        data-aos="fade-up" data-aos-duration="1500" data-aos-offset="50">
                        <h2>Bác sĩ được đánh giá cao</h2>
                        <p>Kết nối với đội ngũ bác sĩ giàu kinh nghiệm từ nhiều chuyên khoa khác nhau.</p>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row justify-content-center">
                    @foreach ($doctorsPopular as $doctor)
                    <div class="col-xl-3 col-md-6">
                        <div class="destination-item style-two">
                            <div class="image" style="max-height: 250px">
                                <!-- <img src="{{ asset('uploads/doctors/' . $doctor->avatar) }}"
                                    alt="{{ $doctor->name }}"> -->
                            </div>
                            <div class="content">
                                <h6>
                                    <a href="{{ route('doctor.detail', $doctor->id) }}">
                                        {{ $doctor->name }}
                                    </a>
                                </h6>
                                <span class="time">
                                    <i class="fas fa-star text-warning"></i>
                                    {{ $doctor->rating ?? 0 }}
                                    <!-- ({{ $doctor->reviews_count ?? 0 }} đánh giá) -->
                                </span>
                                <a href="{{ route('doctor.detail', $doctor->id) }}" class="more">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Popular Destinations Area end -->


<!-- Features Area start -->
<section class="features-area pt-100 pb-45 rel z-1">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-6">
                <div class="features-content-part mb-55" data-aos="fade-left"
                    data-aos-duration="1500" data-aos-offset="50">
                    <div class="section-title mb-60">
                        <h2>Dịch vụ y tế hiện đại mang đến sự khác biệt cho sức khỏe của bạn</h2>
                    </div>
                    <div class="features-customer-box">
                        <div class="image">
                            <img src="{{ asset('clients/assets/images/features/features-box.jpg') }}" alt="Features">
                        </div>
                        <div class="content">
                            <div class="feature-authors mb-15">
                                <img src="{{ asset('clients/assets/images/features/feature-author1.jpg') }}" alt="Author">
                                <img src="{{ asset('clients/assets/images/features/feature-author2.jpg') }}" alt="Author">
                                <img src="{{ asset('clients/assets/images/features/feature-author3.jpg') }}" alt="Author">
                                <span>4k+</span>
                            </div>
                            <h6>10K+ Bệnh nhân hài lòng</h6>
                            <div class="divider style-two counter-text-wrap my-25">
                                <span>
                                    <span class="count-text plus" data-speed="3000" data-stop="5">0</span>
                                    Năm kinh nghiệm
                                </span>
                            </div>
                            <p>Chúng tôi tự hào kết nối bệnh nhân với bác sĩ uy tín trên toàn quốc</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6" data-aos="fade-right" data-aos-duration="1500" data-aos-offset="50">
                <div class="row pb-25">
                    <div class="col-md-6">
                        <div class="feature-item">
                            <div class="icon"><i class="flaticon-tent"></i></div>
                            <div class="content">
                                <h5><a href="{{ route('appointment.create') }}">Đặt lịch trực tuyến</a></h5>
                                <p>Đặt lịch khám chỉ trong vài giây.</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="icon"><i class="flaticon-tent"></i></div>
                            <div class="content">
                                <h5><a href="{{ route('doctors') }}">Tư vấn từ xa</a></h5>
                                <p>Trao đổi với bác sĩ qua video hoặc chat.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-item mt-20">
                            <div class="icon"><i class="flaticon-tent"></i></div>
                            <div class="content">
                                <h5><a href="{{ route('doctors') }}">Bác sĩ uy tín</a></h5>
                                <p>Thông tin minh bạch và đánh giá thực tế.</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="icon"><i class="flaticon-tent"></i></div>
                            <div class="content">
                                <h5><a href="{{ route('appointments.index') }}">Hồ sơ sức khỏe</a></h5>
                                <p>Lưu trữ lịch sử khám bệnh an toàn.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Features Area end -->


<!-- CTA Area start -->
<section class="cta-area pt-100 rel z-1">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-4 col-md-6" data-aos="zoom-in-down"
                data-aos-duration="1500" data-aos-offset="50">
                <div class="cta-item"
                    style="background-image: url({{ asset('clients/assets/images/cta/cta1.jpg') }});">
                    <span class="category">Khám trực tuyến</span>
                    <h2>Nhận tư vấn y tế mọi lúc mọi nơi</h2>
                    <a href="{{ route('doctors') }}" class="theme-btn style-two bgc-secondary">
                        <span data-hover="Khám phá">Khám phá</span>
                        <i class="fal fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-xl-4 col-md-6" data-aos="zoom-in-down" data-aos-delay="50"
                data-aos-duration="1500" data-aos-offset="50">
                <div class="cta-item"
                    style="background-image: url({{ asset('clients/assets/images/cta/cta2.jpg') }});">
                    <span class="category">Đặt lịch nhanh</span>
                    <h2>Tìm bác sĩ phù hợp với nhu cầu của bạn</h2>
                    <a href="{{ route('appointment.create') }}" class="theme-btn style-two">
                        <span data-hover="Đặt lịch ngay">Đặt lịch ngay</span>
                        <i class="fal fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-xl-4 col-md-6" data-aos="zoom-in-down" data-aos-delay="100"
                data-aos-duration="1500" data-aos-offset="50">
                <div class="cta-item"
                    style="background-image: url({{ asset('clients/assets/images/cta/cta3.jpg') }});">
                    <span class="category">Chuyên khoa đa dạng</span>
                    <h2>Kết nối với hàng trăm bác sĩ trên toàn quốc</h2>
                    <a href="{{ route('specialties.index') }}" class="theme-btn style-two bgc-secondary">
                        <span data-hover="Xem chuyên khoa">Xem chuyên khoa</span>
                        <i class="fal fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- CTA Area end -->


@include('clients.blocks.footer_home')