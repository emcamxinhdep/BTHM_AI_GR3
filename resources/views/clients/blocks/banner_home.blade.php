<!-- Hero Area Start -->
<section class="hero-area bgc-black pt-200 rpt-120 rel z-2">
    <div class="container-fluid">
        <h1 class="hero-title" data-aos="flip-up" data-aos-delay="50"
            data-aos-duration="1500" data-aos-offset="50">
            Đặt Lịch Khám
        </h1>
        <div class="main-hero-image bgs-cover"
            style="background-image: url({{ asset('clients/assets/images/hero/hero.jpg') }});">
        </div>
    </div>

    <form action="{{ route('doctors.search') }}" method="GET" id="search_form">
        <div class="container container-1400">
            <div class="search-filter-inner" data-aos="zoom-out-down"
                data-aos-duration="1500" data-aos-offset="50">

                <div class="filter-item clearfix">
                    <div class="icon"><i class="fal fa-stethoscope"></i></div>
                    <span class="title">Chuyên khoa</span>
                    <select name="specialty_id" id="specialty_id">
                        <option value="">Tất cả chuyên khoa</option>
                        @foreach($specialties as $specialty)
                            <option value="{{ $specialty->id }}">
                                {{ $specialty->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-item clearfix">
                    <div class="icon"><i class="fal fa-search"></i></div>
                    <span class="title">Tên bác sĩ / Triệu chứng</span>
                    <input type="text" name="keyword"
                        placeholder="Nhập tên bác sĩ hoặc triệu chứng...">
                </div>

                <div class="filter-item clearfix">
                    <div class="icon"><i class="fal fa-calendar-alt"></i></div>
                    <span class="title">Ngày khám</span>
                    <input type="text" id="date" name="date"
                        class="datetimepicker datetimepicker-custom"
                        placeholder="Chọn ngày khám" readonly>
                </div>

                <div class="search-button">
                    <button class="theme-btn" type="submit">
                        <span data-hover="Tìm kiếm">Tìm kiếm</span>
                        <i class="far fa-search"></i>
                    </button>
                </div>

            </div>
        </div>
    </form>
</section>
<!-- Hero Area End -->