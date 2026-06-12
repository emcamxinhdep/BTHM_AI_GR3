<?php

namespace App\Models\clients;

use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{
    protected $table = 'specialties';

    protected $fillable = [
        'name', 'slug', 'icon', 'image', 'description', 'status',
    ];

    public function doctors()
    {
        return $this->hasMany(Doctor::class, 'specialty_id');
    }
}