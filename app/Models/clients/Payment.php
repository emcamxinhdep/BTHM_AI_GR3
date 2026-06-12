<?php

namespace App\Models\clients;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'appointment_id', 'patient_id', 'amount',
        'method', 'transaction_id', 'order_id',
        'request_id', 'raw_response', 'status',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}