<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class AdminModel extends Model
{
    protected $table = 'admins';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $guarded = [];

    public function getAdmin()
    {
        return $this->where('id', session('admin_id', 1))->first();
    }

    public function updateAdmin($dataUpdate)
    {
        return $this->where('id', session('admin_id', 1))->update($dataUpdate);
    }
}