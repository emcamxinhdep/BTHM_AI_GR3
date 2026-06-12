@include('clients.blocks.header')
@include('clients.blocks.banner')


<!-- About Area start -->
<section class="about-area-two py-100 rel z-1">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-xl-3" data-aos="fade-right" data-aos-duration="1500" data-aos-offset="50">
                <span class="subtitle mb-35">Về chúng tôi</span>
            </div>
            <div class="col-xl-9">
                <div class="about-page-content" data-aos="fade-left" data-aos-duration="1500" data-aos-offset="50">
                    <div class="row">
                        <div class="col-lg-8 pe-lg-5 me-lg-5">
                            <div class="section-title mb-25">
                                <h2>Hệ thống đặt lịch khám bệnh trực tuyến hàng đầu Việt Nam</h2>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="experience-years rmb-20">
                                <span class="title bgc-secondary">Năm kinh nghiệm</span>
                                <span class="text">Chúng tôi có </span>
                                <span class="years">10+</span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <p>Chúng tôi cung cấp nền tảng đặt lịch khám bệnh trực tuyến hiện đại, giúp bệnh nhân dễ
                                dàng kết nối với đội ngũ bác sĩ chuyên khoa uy tín trên toàn quốc. Hệ thống hỗ trợ đặt
                                lịch 24/7, nhắc lịch tự động và thanh toán trực tuyến an toàn, tiện lợi.</p>
                            <ul class="list-style-two mt-35">
                                <li>Đặt lịch nhanh chóng, tiện lợi</li>
                                <li>Đội ngũ bác sĩ chuyên nghiệp</li>
                                <li>Chi phí khám minh bạch</li>
                                <li>Hỗ trợ trực tuyến 24/7</li>
                            </ul>
                            <a href="{{ route('appointment.create') }}" class="theme-btn style-three mt-30">
                                <span data-hover="Đặt Lịch Ngay">Đặt Lịch Ngay</span>
                                <i class="fal fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- About Area end -->


<!-- Features Area start -->
<section class="about-features-area">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-4 col-md-6">
                <div class="about-feature-image" data-aos="fade-up" data-aos-duration="1500" data-aos-offset="50">
                    <img src="{{ asset('clients/assets/images/about/about-feature1.jpg') }}" alt="Bác sĩ tư vấn">
                </div>
            </div>
            <div class="col-xl-4 col-md-6">
                <div class="about-feature-image" data-aos="fade-up" data-aos-delay="50" data-aos-duration="1500"
                    data-aos-offset="50">
                    <img src="{{ asset('clients/assets/images/about/about-feature2.jpg') }}" alt="Khám bệnh">
                </div>
            </div>
            <div class="col-xl-4 col-md-8">
                <div class="about-feature-boxes" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1500"
                    data-aos-offset="50">
                    <div class="feature-item style-three bgc-secondary">
                        <div class="icon-title">
                            <div class="icon"><i class="flaticon-award-symbol"></i></div>
                            <h5><a href="#">Dịch vụ y tế chất lượng cao</a></h5>
                        </div>
                        <div class="content">
                            <p>Được hàng nghìn bệnh nhân tin tưởng, chúng tôi cam kết mang lại trải nghiệm khám chữa bệnh tốt nhất.</p>
                        </div>
                    </div>
                    <div class="feature-item style-three bgc-primary">
                        <div class="icon-title">
                            <div class="icon"><i class="flaticon-doctor"></i></div>
                            <h5><a href="#">100+ Bác sĩ chuyên khoa uy tín</a></h5>
                        </div>
                        <div class="content">
                            <p>Đội ngũ bác sĩ giàu kinh nghiệm, tận tâm chăm sóc và bảo vệ sức khỏe của bạn mỗi ngày.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Features Area end -->


<!-- About Us Area start -->
<section class="about-us-area pt-70 pb-100 rel z-1">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-5 col-lg-6">
                <div class="about-us-content rmb-55" data-aos="fade-left" data-aos-duration="1500" data-aos-offset="50">
                    <div class="section-title mb-25">
                        <h2>Lý do hàng nghìn bệnh nhân tin tưởng và lựa chọn chúng tôi</h2>
                    </div>
                    <p>Chúng tôi đồng hành cùng bệnh nhân từ lúc đặt lịch đến sau khi khám, đảm bảo trải nghiệm y tế
                        thuận tiện, an toàn và hiệu quả. Mỗi bệnh nhân đều được tư vấn tận tình và chăm sóc chu đáo
                        bởi đội ngũ y bác sĩ chuyên nghiệp.</p>
                    <div class="row pt-25">
                        <div class="col-6">
                            <div class="counter-item counter-text-wrap">
                                <span class="count-text k-plus" data-speed="2000" data-stop="50">0</span>
                                <span class="counter-title">Bệnh nhân đã khám</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="counter-item counter-text-wrap">
                                <span class="count-text plus" data-speed="3000" data-stop="100">0</span>
                                <span class="counter-title">Bác sĩ chuyên khoa</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('doctors.index') }}" class="theme-btn mt-10 style-two">
                        <span data-hover="Xem Đội Ngũ Bác Sĩ">Xem Đội Ngũ Bác Sĩ</span>
                        <i class="fal fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-xl-7 col-lg-6" data-aos="fade-right" data-aos-duration="1500" data-aos-offset="50">
                <div class="about-us-page">
                    <img src="{{ asset('clients/assets/images/about/about-page.jpg') }}" alt="Phòng khám">
                </div>
            </div>
        </div>
    </div>
</section>
<!-- About Us Area end -->


<!-- Team Area start
<section class="about-team-area pb-70 rel z-1">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="section-title text-center counter-text-wrap mb-50" data-aos="fade-up"
                    data-aos-duration="1500" data-aos-offset="50">
                    <h2>Gặp gỡ đội ngũ bác sĩ chuyên khoa của chúng tôi</h2>
                    <p>Hơn <span class="count-text plus bgc-primary" data-speed="3000" data-stop="10000">0</span>
                        lượt khám thành công, bệnh nhân luôn hài lòng và tin tưởng</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">

            <div class="col-xl-3 col-lg-4 col-sm-6">
                <div class="team-item hover-content" data-aos="fade-up" data-aos-duration="1500"
                    data-aos-offset="50">
                    <img src="{{ asset('clients/assets/images/team/guide-dien.jpg') }}" alt="Bác sĩ">
                    <div class="content">
                        <h6>NGUYEN MINH DIEN</h6>
                        <span class="designation">Bác sĩ Nội khoa</span>
                        <div class="social-style-one inner-content">
                            <a href="{{ route('contact') }}"><i class="fab fa-twitter"></i></a>
                            <a href="https://www.facebook.com/dienne.dev"><i class="fab fa-facebook-f"></i></a>
                            <a href="{{ route('contact') }}"><i class="fab fa-instagram"></i></a>
                            <a href="https://www.youtube.com/@dienne248"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-sm-6">
                <div class="team-item hover-content" data-aos="fade-up" data-aos-duration="1500"
                    data-aos-offset="50">
                    <img src="{{ asset('clients/assets/images/team/guide-ngan.jpg') }}" alt="Bác sĩ">
                    <div class="content">
                        <h6>BAO NGAN</h6>
                        <span class="designation">Bác sĩ Da liễu</span>
                        <div class="social-style-one inner-content">
                            <a href="{{ route('contact') }}"><i class="fab fa-twitter"></i></a>
                            <a href="https://www.facebook.com/dienne.dev"><i class="fab fa-facebook-f"></i></a>
                            <a href="{{ route('contact') }}"><i class="fab fa-instagram"></i></a>
                            <a href="https://www.youtube.com/@dienne248"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section> -->
<!-- Team Area end -->


<!-- Features Area start -->
<section class="about-feature-two bgc-black pt-100 pb-45 rel z-1">
    <div class="container">
        <div class="section-title text-center text-white counter-text-wrap mb-50" data-aos="fade-up"
            data-aos-duration="1500" data-aos-offset="50">
            <h2>Quy trình đặt lịch khám bệnh đơn giản chỉ 4 bước</h2>
            <p>Hơn <span class="count-text plus" data-speed="3000" data-stop="10000">0</span> lượt đặt lịch thành công,
                bệnh nhân luôn hài lòng với dịch vụ</p>
        </div>
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-6" data-aos="fade-up" data-aos-duration="1500"
                data-aos-offset="50">
                <div class="feature-item style-two">
                    <div class="icon"><i class="flaticon-search"></i></div>
                    <div class="content">
                        <h5><a href="{{ route('doctors.index') }}">Tìm bác sĩ phù hợp</a></h5>
                        <p>Tìm kiếm bác sĩ theo chuyên khoa, khu vực và đánh giá thực tế từ bệnh nhân.</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="50" data-aos-duration="1500"
                data-aos-offset="50">
                <div class="feature-item style-two">
                    <div class="icon"><i class="flaticon-booking"></i></div>
                    <div class="content">
                        <h5><a href="{{ route('appointment.create') }}">Chọn ngày & giờ khám</a></h5>
                        <p>Xem lịch trống của bác sĩ và chọn khung giờ phù hợp nhất với lịch của bạn.</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1500"
                data-aos-offset="50">
                <div class="feature-item style-two">
                    <div class="icon"><i class="flaticon-save-money"></i></div>
                    <div class="content">
                        <h5><a href="{{ route('appointment.create') }}">Thanh toán nhanh chóng</a></h5>
                        <p>Thanh toán trực tuyến qua MoMo hoặc thanh toán trực tiếp tại phòng khám dễ dàng.</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="150" data-aos-duration="1500"
                data-aos-offset="50">
                <div class="feature-item style-two">
                    <div class="icon"><i class="flaticon-guidepost"></i></div>
                    <div class="content">
                        <h5><a href="{{ route('appointment.create') }}">Nhận xác nhận lịch hẹn</a></h5>
                        <p>Nhận thông báo xác nhận và nhắc lịch tự động, đảm bảo bạn không bỏ lỡ buổi khám.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="shape">
        <img src="{{ asset('clients/assets/images/video/shape1.png') }}" alt="shape">
    </div>
</section>
<!-- Features Area end -->

<!-- Client Logo Area start -->
<!-- <div class="client-logo-area mb-100">
    <div class="container">
        <div class="client-logo-wrap pt-60 pb-55">
            <div class="text-center mb-40" data-aos="zoom-in" data-aos-duration="1500" data-aos-offset="50">
                <h6>Đối tác bệnh viện & phòng khám uy tín:</h6>
            </div>
            <div class="client-logo-active">
                <div class="client-logo-item" data-aos="flip-up" data-aos-duration="1500" data-aos-offset="50">
                    <a href="#"><img src="{{ asset('clients/assets/images/client-logos/client-logo1.png') }}"
                            alt="Đối tác 1"></a>
                </div>
                <div class="client-logo-item" data-aos="flip-up" data-aos-delay="50" data-aos-duration="1500"
                    data-aos-offset="50">
                    <a href="#"><img src="{{ asset('clients/assets/images/client-logos/client-logo2.png') }}"
                            alt="Đối tác 2"></a>
                </div>
                <div class="client-logo-item" data-aos="flip-up" data-aos-delay="100" data-aos-duration="1500"
                    data-aos-offset="50">
                    <a href="#"><img src="{{ asset('clients/assets/images/client-logos/client-logo3.png') }}"
                            alt="Đối tác 3"></a>
                </div>
                <div class="client-logo-item" data-aos="flip-up" data-aos-delay="150" data-aos-duration="1500"
                    data-aos-offset="50">
                    <a href="#"><img src="{{ asset('clients/assets/images/client-logos/client-logo4.png') }}"
                            alt="Đối tác 4"></a>
                </div>
                <div class="client-logo-item" data-aos="flip-up" data-aos-delay="200" data-aos-duration="1500"
                    data-aos-offset="50">
                    <a href="#"><img src="{{ asset('clients/assets/images/client-logos/client-logo5.png') }}"
                            alt="Đối tác 5"></a>
                </div>
            </div>
        </div>
    </div>
</div> -->
<!-- Client Logo Area end -->


@include('clients.blocks.footer')