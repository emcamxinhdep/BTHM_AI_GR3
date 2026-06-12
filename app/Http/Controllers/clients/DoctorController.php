<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\clients\Doctor;
use App\Models\clients\Specialty;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $query = Doctor::with('specialty')->where('status', 1);

        if ($request->specialty_id) {
            $query->where('specialty_id', $request->specialty_id);
        }

        if ($request->district) {
            $query->where('clinic_district', $request->district);
        }

        if ($request->fee_max) {
            $query->where('consultation_fee', '<=', $request->fee_max);
        }

        $doctors     = $query->orderBy('rating', 'desc')->get();
        $specialties = Specialty::where('status', 1)->get();

        if (session('patient_lat') && session('patient_lng')) {
            $doctors = $doctors->map(function ($doctor) {
                $doctor->distance = $doctor->distanceTo(
                    session('patient_lat'),
                    session('patient_lng')
                );
                return $doctor;
            })->sortBy('distance')->values();
        }

        return view('clients.doctors.index', compact('doctors', 'specialties'))
            ->with('title', 'Danh sách bác sĩ');
    }

    public function detail($id)
    {
        $doctor = Doctor::with(['specialty', 'reviews.patient', 'appointments'])
            ->where('status', 1)
            ->findOrFail($id);

        $workingHours = $doctor->getWorkingHoursArray();

        return view('clients.doctors.detail', compact('doctor', 'workingHours'))
            ->with('title', ' ' . $doctor->name);
    }

    public function search(Request $request)
    {
        $keyword     = $request->keyword;
        $specialtyId = $request->specialty_id;
        $date        = $request->date;

        $query = Doctor::with('specialty')->where('status', 1);

        if ($keyword) {
            $mappedSpecialty = $this->mapSymptomToSpecialty($keyword);
            if ($mappedSpecialty) {
                $query->where('specialty_id', $mappedSpecialty);
            } else {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                      ->orWhere('description', 'like', "%{$keyword}%");
                });
            }
        }

        if ($specialtyId) {
            $query->where('specialty_id', $specialtyId);
        }

        $doctors     = $query->orderBy('rating', 'desc')->get();
        $specialties = Specialty::where('status', 1)->get();

        return view('clients.doctors.index', compact('doctors', 'specialties', 'keyword', 'date'))
            ->with('title', 'Tìm kiếm bác sĩ');
    }

    private function mapSymptomToSpecialty(string $keyword): ?int
    {
        $csvPath = storage_path('app/data/symptom_specialty_mapping.csv');
        if (!file_exists($csvPath)) return null;

        $keyword = mb_strtolower($keyword);
        $handle  = fopen($csvPath, 'r');
        fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {
            if (str_contains($keyword, mb_strtolower($row[0]))) {
                fclose($handle);
                return (int) $row[1];
            }
        }

        fclose($handle);
        return null;
    }
}