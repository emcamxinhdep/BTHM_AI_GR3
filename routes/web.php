<?php

use Illuminate\Support\Facades\Route;

// CLIENT
use App\Http\Controllers\clients\HomeController;
use App\Http\Controllers\clients\DoctorController;
use App\Http\Controllers\clients\ClinicController;
use App\Http\Controllers\clients\SpecialtyController;
use App\Http\Controllers\clients\AppointmentController;
use App\Http\Controllers\clients\AboutController;
use App\Http\Controllers\clients\ContactController;
use App\Http\Controllers\clients\LoginController;
use App\Http\Controllers\clients\UserProfileController;
use App\Http\Controllers\clients\PaymentController;
use App\Http\Controllers\clients\ReviewController;
use App\Http\Controllers\clients\LoginGoogleController;

// ADMIN
use App\Http\Controllers\admin\LoginAdminController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\AdminManagementController;
use App\Http\Controllers\admin\UserManagementController;
use App\Http\Controllers\admin\DoctorsManagementController;
use App\Http\Controllers\admin\AppointmentManagementController;
use App\Http\Controllers\admin\ClinicManagementController;
use App\Http\Controllers\admin\SpecialtyManagementController;

/*
|--------------------------------------------------------------------------
| CLIENT ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Chuyên khoa
Route::get('/specialties', [SpecialtyController::class, 'index'])->name('specialties.index');
Route::get('/specialty/{id}', [SpecialtyController::class, 'detail'])->name('specialty.detail');

// Bác sĩ
Route::get('/doctors', [DoctorController::class, 'index'])
    ->name('doctors.index');
Route::get('/doctor/{id}', [DoctorController::class, 'detail'])->name('doctor.detail');
Route::get('/doctors/search', [DoctorController::class, 'search'])->name('doctors.search');
Route::get('/doctor-edit/{id}', [DoctorsManagementController::class, 'edit'])->name('admin.doctor-edit');
Route::post('/doctor-update/{id}', [DoctorsManagementController::class, 'update'])->name('admin.doctor-update');

// Giới thiệu & Liên hệ
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('user-login');
Route::post('/register', [LoginController::class, 'register'])->name('register');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Google OAuth
Route::get('/auth/google', [LoginGoogleController::class, 'redirectToGoogle'])->name('login-google');
Route::get('/auth/google/callback', [LoginGoogleController::class, 'handleGoogleCallback'])->name('login-google-callback');

/*
|--------------------------------------------------------------------------
| USER (requires login)
|--------------------------------------------------------------------------
*/

Route::middleware('checkLoginClient')->group(function () {

    // Profile
    Route::get('/user-profile', [UserProfileController::class, 'index'])->name('user-profile');
    Route::post('/user-profile', [UserProfileController::class, 'update'])->name('update-user-profile');
    Route::post('/change-password-profile', [UserProfileController::class, 'changePassword'])->name('change-password');
    Route::post('/change-avatar-profile', [UserProfileController::class, 'changeAvatar'])->name('change-avatar');

    // Đặt lịch hẹn
    Route::get('/appointment/book', [AppointmentController::class, 'create'])->name('appointment.create');
    Route::post('/appointment/check-slots', [AppointmentController::class, 'checkSlots'])->name('appointment.check-slots');
    Route::post('/appointment/store', [AppointmentController::class, 'store'])->name('appointment.store');
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointment/{id}', [AppointmentController::class, 'show'])->name('appointment.show');
    Route::post('/appointment/{id}/cancel', [AppointmentController::class, 'cancel'])->name('appointment.cancel');

    // Thanh toán MoMo
    Route::post('/payment/momo', [PaymentController::class, 'createMomo'])->name('payment.momo');
    Route::get('/payment/momo/return', [PaymentController::class, 'momoReturn'])->name('payment.momo.return');
    Route::post('/payment/momo/notify', [PaymentController::class, 'momoNotify'])->name('payment.momo.notify');

    // Đánh giá bác sĩ
    Route::post('/review/store', [ReviewController::class, 'store'])->name('review.store');
});

/*
|--------------------------------------------------------------------------
| ADMIN LOGIN
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->group(function () {
    Route::get('/', [LoginAdminController::class, 'index'])->name('admin.login');
    Route::get('/login', [LoginAdminController::class, 'index'])->name('admin.login');
    Route::post('/login-account', [LoginAdminController::class, 'loginAdmin'])->name('admin.login-account');
    Route::post('/logout', [LoginAdminController::class, 'logout'])->name('admin.logout');
});

/*
|--------------------------------------------------------------------------
| ADMIN PANEL
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware('admin')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Admin profile
    Route::get('/admin-profile', [AdminManagementController::class, 'index'])->name('admin.profile');
    Route::post('/update-admin', [AdminManagementController::class, 'updateAdmin'])->name('admin.update-admin');
    Route::post('/update-avatar', [AdminManagementController::class, 'updateAvatar'])->name('admin.update-avatar');

    // Bệnh nhân
    Route::get('/patients', [UserManagementController::class, 'index'])->name('admin.patients');
    Route::post('/status-patient', [UserManagementController::class, 'changeStatus'])->name('admin.status-patient');

    // Bác sĩ
    Route::get('/doctors', [DoctorsManagementController::class, 'index'])->name('admin.doctors');
    Route::get('/doctor-add', [DoctorsManagementController::class, 'create'])->name('admin.doctor-add');
    Route::post('/doctor-store', [DoctorsManagementController::class, 'store'])->name('admin.doctor-store');
    Route::get('/doctor-edit/{id}', [DoctorsManagementController::class, 'edit'])->name('admin.doctor-edit');
    Route::post('/doctor-update/{id}', [DoctorsManagementController::class, 'update'])->name('admin.doctor-update');
    Route::post('/doctor-delete', [DoctorsManagementController::class, 'delete'])->name('admin.doctor-delete');

    // Chuyên khoa
    Route::get('/specialties', [SpecialtyManagementController::class, 'index'])->name('admin.specialties');
    Route::post('/specialty-store', [SpecialtyManagementController::class, 'store'])->name('admin.specialty-store');
    Route::post('/specialty-update/{id}', [SpecialtyManagementController::class, 'update'])->name('admin.specialty-update');
    Route::post('/specialty-delete', [SpecialtyManagementController::class, 'delete'])->name('admin.specialty-delete');

    // Lịch hẹn
    Route::get('/appointments', [AppointmentManagementController::class, 'index'])->name('admin.appointments.index');
    Route::post('/appointments/{id}/confirm', [AppointmentManagementController::class, 'confirm'])->name('admin.appointments.confirm');
    Route::post('/appointments/{id}/complete', [AppointmentManagementController::class, 'finish'])->name('admin.appointments.complete');
    Route::post('/appointments/{id}/cancel', [AppointmentManagementController::class, 'cancel'])->name('admin.appointments.cancel');
    Route::post('/appointments/{id}/send-notification', [AppointmentManagementController::class, 'sendNotification'])->name('admin.appointments.sendNotification'); // ← bỏ /admin/ thừa
});