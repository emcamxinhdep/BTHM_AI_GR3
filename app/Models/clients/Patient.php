<?php

namespace App\Models\clients;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Patient extends Authenticatable
{
    protected $table = 'patients';

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'avatar',
        'birthday', 'gender', 'address', 'city',
        'latitude', 'longitude', 'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'birthday' => 'date',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'patient_id');
    }

    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function getPhoneNumberAttribute()
    {
        return $this->phone;
    }
}