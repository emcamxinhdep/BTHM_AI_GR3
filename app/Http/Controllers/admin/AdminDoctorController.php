<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\clients\Doctor;
use App\Models\clients\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminDoctorController extends Controller
{
    public function index(Request $request)
    {
        $query = Doctor::with('specialty')->withCount('appointments');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('clinic_name', 'like', "%$s%");
            });
        }
        if ($request->filled('specialty_id')) {
            $query->where('specialty_id', $request->specialty_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $doctors    = $query->orderBy('name')->paginate(12)->withQueryString();
        $specialties = Specialty::where('status', 1)->get();

        return view('admin.doctors.index', compact('doctors', 'specialties'));
    }

    public function create()
    {
        $specialties = Specialty::where('status', 1)->get();
        return view('admin.doctors.form', compact('specialties'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'specialty_id'     => 'required|exists:specialties,id',
            'name'             => 'required|string|max:100',
            'email'            => 'required|email|unique:doctors,email',
            'phone'            => 'nullable|string|max:20',
            'degree'           => 'nullable|string|max:50',
            'clinic_name'      => 'nullable|string|max:200',
            'clinic_address'   => 'nullable|string|max:300',
            'clinic_district'  => 'nullable|string|max:100',
            'clinic_city'      => 'nullable|string|max:100',
            'experience_years' => 'nullable|integer|min:0',
            'consultation_fee' => 'nullable|numeric|min:0',
            'description'      => 'nullable|string',
            'working_hours'    => 'nullable|string',
            'status'           => 'required|in:0,1',
            'avatar'           => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('doctors', 'public');
        }

        if ($request->filled('working_hours')) {
            $data['working_hours'] = $request->working_hours; // JSON string
        }

        Doctor::create($data);

        return redirect()->route('admin.doctors.index')->with('success', 'Đã thêm bác sĩ thành công.');
    }

    public function edit($id)
    {
        $doctor      = Doctor::findOrFail($id);
        $specialties = Specialty::where('status', 1)->get();
        return view('admin.doctors.form', compact('doctor', 'specialties'));
    }

    public function update(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);

        $data = $request->validate([
            'specialty_id'     => 'required|exists:specialties,id',
            'name'             => 'required|string|max:100',
            'email'            => 'required|email|unique:doctors,email,' . $id,
            'phone'            => 'nullable|string|max:20',
            'degree'           => 'nullable|string|max:50',
            'clinic_name'      => 'nullable|string|max:200',
            'clinic_address'   => 'nullable|string|max:300',
            'clinic_district'  => 'nullable|string|max:100',
            'clinic_city'      => 'nullable|string|max:100',
            'experience_years' => 'nullable|integer|min:0',
            'consultation_fee' => 'nullable|numeric|min:0',
            'description'      => 'nullable|string',
            'working_hours'    => 'nullable|string',
            'status'           => 'required|in:0,1',
            'avatar'           => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('doctors', 'public');
        }

        $doctor->update($data);

        return redirect()->route('admin.doctors.index')->with('success', 'Đã cập nhật bác sĩ.');
    }

    public function destroy($id)
    {
        Doctor::findOrFail($id)->delete();
        return back()->with('success', 'Đã xóa bác sĩ.');
    }
}
