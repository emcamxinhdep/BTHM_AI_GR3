<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class AppointmentModel extends Model
{
    protected $table = 'appointments';

    protected $fillable = [
        'patient_id', 'doctor_id',
        'appointment_date', 'appointment_time',
        'symptoms', 'note', 'diagnosis',
        'fee', 'payment_method', 'payment_status',
        'payment_transaction_id', 'status', 'reminder_sent',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'reminder_sent'    => 'boolean',
    ];

    public function doctor()
    {
        return $this->belongsTo(DoctorModel::class, 'doctor_id');
    }

    public function patient()
    {
        return $this->belongsTo(PatientModel::class, 'patient_id');
    }
}