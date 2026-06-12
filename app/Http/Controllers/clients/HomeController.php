<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\clients\Doctor;
use App\Models\clients\Specialty;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Bỏ take(8) để banner có đủ chuyên khoa
        $specialties = Specialty::where('status', 1)->get();

        $doctors = Doctor::with('specialty')
            ->where('status', 1)
            ->orderBy('rating', 'desc')
            ->take(8)
            ->get();

        $doctorsPopular = Doctor::with('specialty')
            ->where('status', 1)
            ->orderBy('rating', 'desc')
            ->take(4)
            ->get();

        $suggestedDoctors = collect();
        if (session('patient_id')) {
            $bookedSpecialtyIds = \App\Models\clients\Appointment::where('patient_id', session('patient_id'))
                ->with('doctor')
                ->get()
                ->pluck('doctor.specialty_id')
                ->unique();

            $suggestedDoctors = Doctor::with('specialty')
                ->whereIn('specialty_id', $bookedSpecialtyIds)
                ->where('status', 1)
                ->orderBy('rating', 'desc')
                ->take(4)
                ->get();
        }

        return view('clients.home', compact(
            'specialties',
            'doctors',
            'doctorsPopular',
            'suggestedDoctors'
        ))->with('title', 'Trang chủ');
    }
}