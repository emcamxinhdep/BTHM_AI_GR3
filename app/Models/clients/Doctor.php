<?php

namespace App\Models\clients;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
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
        return $this->belongsTo(Specialty::class, 'specialty_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'doctor_id');
    }

    /**
     * Tính khoảng cách từ bác sĩ đến bệnh nhân (km)
     */
    public function distanceTo(float $lat, float $lng): float
    {
        if (!$this->latitude || !$this->longitude) return 999;

        $earthRadius = 6371;
        $dLat = deg2rad($this->latitude - $lat);
        $dLng = deg2rad($this->longitude - $lng);
        $a = sin($dLat/2) ** 2 +
             cos(deg2rad($lat)) * cos(deg2rad($this->latitude)) *
             sin($dLng/2) ** 2;
        return round($earthRadius * 2 * atan2(sqrt($a), sqrt(1-$a)), 1);
    }

    /**
     * Lấy danh sách slot từ working_hours JSON
     */
    public function getWorkingHoursArray(): array
    {
        $workingHours = $this->working_hours;
        
        // Nếu là null hoặc rỗng -> mảng rỗng
        if (empty($workingHours)) {
            return [];
        }
        
        // Nếu đã là mảng
        if (is_array($workingHours)) {
            return $workingHours;
        }
        
        // Nếu là string, thử decode JSON
        if (is_string($workingHours)) {
            $decoded = json_decode($workingHours, true);
            // Nếu decode thành công và là mảng
            if (is_array($decoded)) {
                return $decoded;
            }
            // Nếu decode ra scalar (string, int...) thì bọc vào mảng
            if ($decoded !== null) {
                // Trường hợp lưu "Thứ 2: 8h-12h" dưới dạng JSON string
                return ['error' => 'Dữ liệu không đúng định dạng', 'raw' => $decoded];
            }
            // JSON không hợp lệ, trả về mảng rỗng
            return [];
        }
        
        return [];
    }

    public function getHospitalAttribute()
    {
        return $this->clinic_name;
    }

    public function getExperienceAttribute()
    {
        return $this->experience_years;
    }
}