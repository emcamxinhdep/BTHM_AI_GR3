<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class DoctorModel extends Model
{
    protected $table = 'doctors';

    protected $fillable = [
        'specialty_id', 'name', 'email', 'phone', 'avatar',
        'degree', 'clinic_name', 'clinic_address', 'clinic_district',
        'clinic_city', 'latitude', 'longitude',
        'experience_years', 'consultation_fee',
        'rating', 'total_reviews', 'description',
        'working_hours', 'status',
    ];

    public function specialty()
    {
        return $this->belongsTo(
            \App\Models\clients\Specialty::class,
            'specialty_id'
        );
    }

    public function appointments()
    {
        return $this->hasMany(
            \App\Models\clients\Appointment::class,
            'doctor_id'
        );
    }

    public function reviews()
    {
        return $this->hasMany(
            \App\Models\clients\Review::class,
            'doctor_id'
        );
    }
}