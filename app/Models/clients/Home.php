<?php

namespace App\Models\clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Home extends Model
{
    use HasFactory;

    protected $table      = 'doctors';
    protected $primaryKey = 'doctor_id';

    public function getHomeDoctors()
    {
        $doctors = DB::table('doctors')
            ->leftJoin('specialties', 'doctors.specialty_id', '=', 'specialties.specialty_id')
            ->where('doctors.status', 1)
            ->select(
                'doctors.*',
                'specialties.specialty_name as specialty'
            )
            ->take(8)
            ->get();

        foreach ($doctors as $doctor) {
            $stats = DB::table('reviews')
                ->where('doctor_id', $doctor->doctor_id)
                ->selectRaw('ROUND(AVG(rating), 1) as averageRating, COUNT(*) as totalReviews')
                ->first();

            $doctor->rating       = $stats->averageRating ?? 0;
            $doctor->totalReviews = $stats->totalReviews  ?? 0;
        }

        return $doctors;
    }
}