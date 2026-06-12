<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\clients\Doctor;
use App\Models\clients\Specialty;

class AboutController extends Controller
{
    public function index()
    {
        $totalDoctors     = Doctor::where('status', 1)->count();
        $totalSpecialties = Specialty::where('status', 1)->count();

        return view('clients.about', compact('totalDoctors', 'totalSpecialties'))
            ->with('title', 'Giới thiệu');
    }
}