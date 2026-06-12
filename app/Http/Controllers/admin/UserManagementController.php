<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admin\UserModel;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index()
    {
        $title = 'Quản lý bệnh nhân';

        $query = UserModel::query();

        if (request('search')) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . request('search') . '%')
                  ->orWhere('email', 'like', '%' . request('search') . '%')
                  ->orWhere('phone', 'like', '%' . request('search') . '%');
            });
        }

        if (request('status') !== null && request('status') !== '') {
            $query->where('status', request('status'));
        }

        $patients = $query->paginate(10);

        return view('admin.patients.index', compact('title', 'patients'));
    }

    public function changeStatus(Request $request)
    {
        $patientId = $request->patientId;
        $status    = $request->status;

        $updated = UserModel::where('id', $patientId)->update(['status' => $status]);

        if ($updated) {
            return back()->with('success', 'Cập nhật trạng thái thành công!');
        }

        return back()->with('error', 'Có lỗi xảy ra!');
    }
}