<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\clients\Doctor;
use App\Models\clients\Specialty;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $keyword     = $request->keyword;
        $specialtyId = $request->specialty_id;
        $date        = $request->date;

        $query = Doctor::with('specialty')->where('status', 1);

        if ($keyword) {
            // Map triệu chứng → chuyên khoa
            $mappedSpecialtyId = $this->mapSymptomToSpecialty($keyword);

            if ($mappedSpecialtyId) {
                $query->where('specialty_id', $mappedSpecialtyId);
            } else {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                      ->orWhere('description', 'like', "%{$keyword}%")
                      ->orWhere('clinic_name', 'like', "%{$keyword}%");
                });
            }
        }

        if ($specialtyId) {
            $query->where('specialty_id', $specialtyId);
        }

        $doctors     = $query->orderBy('rating', 'desc')->get();
        $specialties = Specialty::where('status', 1)->get();

        return view('clients.search', compact('doctors', 'specialties', 'keyword', 'date'));
    }

    private function mapSymptomToSpecialty(string $keyword): ?int
    {
        $csvPath = storage_path('app/data/symptom_specialty_mapping.csv');
        if (!file_exists($csvPath)) return null;

        $keyword = mb_strtolower($keyword);
        $handle  = fopen($csvPath, 'r');
        fgetcsv($handle); // bỏ header

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