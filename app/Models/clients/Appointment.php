<?php

namespace App\Models\clients;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
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
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'appointment_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'appointment_id');
    }

    // Label hiển thị trạng thái
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'   => '<span class="badge bg-warning">Chờ xác nhận</span>',
            'confirmed' => '<span class="badge bg-info">Đã xác nhận</span>',
            'completed' => '<span class="badge bg-success">Hoàn thành</span>',
            'cancelled' => '<span class="badge bg-danger">Đã hủy</span>',
            default     => '<span class="badge bg-secondary">Không rõ</span>',
        };
    }
}