<?php

namespace App\Models\admin;

use Illuminate\Foundation\Auth\User as Authenticatable;

class PatientModel extends Authenticatable
{
    protected $table = 'patients';

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'avatar',
        'birthday', 'gender', 'address', 'city',
        'latitude', 'longitude', 'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    public function appointments()
    {
        return $this->hasMany(
            \App\Models\clients\Appointment::class,
            'patient_id'
        );
    }
}