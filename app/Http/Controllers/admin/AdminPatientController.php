<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\clients\Patient;
use Illuminate\Http\Request;

class AdminPatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::withCount('appointments');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $patients = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.patients.index', compact('patients'));
    }

    public function toggle($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->update(['status' => $patient->status ? 0 : 1]);
        return back()->with('success', 'Đã cập nhật trạng thái bệnh nhân.');
    }
}
