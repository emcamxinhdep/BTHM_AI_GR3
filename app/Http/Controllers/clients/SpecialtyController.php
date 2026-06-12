<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\clients\Specialty;
use App\Models\clients\Doctor;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{
    public function index()
    {
        $specialties = Specialty::withCount('doctors')
            ->where('status', 1)
            ->get();

        return view('clients.partials.specialties', compact('specialties'))
            ->with('title', 'Chuyên khoa');
    }

    public function detail($id)
    {
        $specialty = Specialty::findOrFail($id);
        $doctors   = Doctor::with('specialty')
            ->where('specialty_id', $id)
            ->where('status', 1)
            ->orderBy('rating', 'desc')
            ->get();

        return view('clients.partials.specialty-detail', compact('specialty', 'doctors'))
            ->with('title', $specialty->name);
    }
}