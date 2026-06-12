<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    protected $table = 'patients'; // ✅ đúng tên bảng
    protected $primaryKey = 'id';
    public $timestamps = true;

    public function getAllUsers()
    {
        return $this->all();
    }

    public function updateActive($patientId)
    {
        return $this->where('id', $patientId)->update(['isActive' => 'y']);
    }

    public function changeStatus($patientId, $dataUpdate)
    {
        return $this->where('id', $patientId)->update([
            'isActive' => $dataUpdate['status']
        ]);
    }
}