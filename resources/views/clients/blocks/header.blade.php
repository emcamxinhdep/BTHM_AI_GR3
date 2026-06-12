<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Title -->
    <title>DoctorCam - {{ $title ?? 'Trang chủ' }}</title>
    <!-- Favicon Icon -->
    <link rel="shortcut icon" href="{{ asset('clients/assets/images/logos/favicon.png') }}" type="image/x-icon">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet">

    <!-- Flaticon -->
    <link rel="stylesheet" href="{{ asset('clients/assets/css/flaticon.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('clients/assets/css/fontawesome-5.14.0.min.css') }}">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('clients/assets/css/bootstrap.min.css') }}">
    <!-- Magnific Popup -->
    <link rel="stylesheet" href="{{ asset('clients/assets/css/magnific-popup.min.css') }}">
    <!-- Nice Select -->
    <link rel="stylesheet" href="{{ asset('clients/assets/css/nice-select.min.css') }}">
    <!-- jQuery UI -->
    <link rel="stylesheet" href="{{ asset('clients/assets/css/jquery-ui.min.css') }}">
    <!-- Animate -->
    <link rel="stylesheet" href="{{ asset('clients/assets/css/aos.css') }}">
    <!-- Slick -->
    <link rel="stylesheet" href="{{ asset('clients/assets/css/slick.min.css') }}">
    <!-- Main Style -->
    <link rel="stylesheet" href="{{ asset('clients/assets/css/style.css') }}">

    {{-- boxicons --}}
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    {{-- Login  --}}
    <!-- Font Icon -->
    <link rel="stylesheet"
        href="{{ asset('clients/assets/css/css-login/fonts/material-icon/css/material-design-iconic-font.min.css') }}">
    <!-- Main css -->
    <link rel="stylesheet" href="{{ asset('clients/assets/css/css-login/style.css') }}">
    {{-- custom css--}}
    <link rel="stylesheet" href="{{ asset('clients/assets/css/custom-css.css') }}" />

    {{-- User Profile  --}}
    <link rel="stylesheet" href="{{ asset('clients/assets/css/user-profile.css') }}" />

    <!-- Import CSS for Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

</head>

<body>
    <div class="page-wrapper">

        <!-- Preloader -->
        <div class="preloader">
            <div class="custom-loader"></div>
        </div>

        <!-- main header -->
        <header class="main-header header-one">
    <div class="header-upper bg-white py-30 rpy-0">
        <div class="container-fluid clearfix">
            <div class="header-inner rel d-flex align-items-center">

                {{-- Logo --}}
                <div class="logo-outer">
                    <div class="logo">
                        <a href="{{ route('home') }}">
                            <img src="{{ asset('clients/assets/images/logos/logo-two.png') }}"
                                alt="DoctorCam" title="DoctorCam">
                        </a>
                    </div>
                </div>

                {{-- Navigation --}}
                <div class="nav-outer mx-lg-auto ps-xxl-5 clearfix">
                    <nav class="main-menu navbar-expand-lg">
                        <div class="navbar-header">
                            <div class="mobile-logo">
                                <a href="{{ route('home') }}">
                                    <img src="{{ asset('clients/assets/images/logos/logo-two.png') }}"
                                        alt="DoctorCam" title="DoctorCam">
                                </a>
                            </div>
                            <button type="button" class="navbar-toggle"
                                data-bs-toggle="collapse" data-bs-target=".navbar-collapse">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>

                        <div class="navbar-collapse collapse clearfix">
                            <ul class="navigation clearfix">
                                <li class="{{ Request::routeIs('home') ? 'active' : '' }}">
                                    <a href="{{ route('home') }}">Trang chủ</a>
                                </li>
                                <li class="{{ Request::routeIs('about') ? 'active' : '' }}">
                                    <a href="{{ route('about') }}">Giới thiệu</a>
                                </li>
                                <li class="{{ Request::routeIs('doctors', 'doctor.detail') ? 'active' : '' }}">
                                    <a href="{{ route('doctors') }}">Bác sĩ</a>
                                </li>
                                <li class="{{ Request::routeIs('specialties') ? 'active' : '' }}">
                                    <a href="{{ route('specialties.index') }}">Chuyên khoa</a>
                                </li>
                                <li class="{{ Request::routeIs('contact') ? 'active' : '' }}">
                                    <a href="{{ route('contact') }}">Liên hệ</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>

                {{-- Right side: Book button + User menu --}}
                <div class="menu-btns py-10 d-flex align-items-center gap-3">

                    {{-- Đặt lịch khám --}}
                    <a href="{{ route('appointment.create') }}"
                        class="theme-btn style-two bgc-secondary">
                        <span data-hover="Đặt lịch">Đặt lịch khám</span>
                        <i class="fal fa-arrow-right"></i>
                    </a>

                    {{-- User Dropdown --}}
                    <div class="menu-sidebar">
                        <ul style="list-style: none; margin: 0; padding: 0;">
                            <li class="drop-down" style="position: relative;">

                                {{-- Avatar / Icon button --}}
                                <button class="dropdown-toggle bg-transparent border-0"
                                    id="userDropdown" style="cursor: pointer;">
                                    @if (session('patient_avatar'))
                                        <img src="{{ asset('clients/assets/images/upload/' . session('patient_avatar')) }}"
                                            class="rounded-circle"
                                            style="width: 36px; height: 36px; object-fit: cover;">
                                    @else
                                        <i class='bx bxs-user-circle'
                                            style="font-size: 36px; color: #333;"></i>
                                    @endif
                                </button>

                                {{-- Dropdown menu --}}
                                <ul class="dropdown-menu" id="dropdownMenu">
                                    @if (session('patient_id'))
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user-profile') }}">
                                                <i class='bx bxs-user me-2'></i>Thông tin cá nhân
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('appointments.index') }}">
                                                <i class='bx bxs-calendar me-2'></i>Lịch khám của tôi
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="{{ route('logout') }}">
                                                <i class='bx bx-log-out me-2'></i>Đăng xuất
                                            </a>
                                        </li>
                                    @else
                                        <li>
                                            <a class="dropdown-item" href="{{ route('login') }}">
                                                <i class='bx bx-log-in me-2'></i>Đăng nhập
                                            </a>
                                        </li>
                                    @endif
                                </ul>

                            </li>
                        </ul>
                    </div>

                </div>
                {{-- End Right side --}}

            </div>
        </div>
    </div>
</header>