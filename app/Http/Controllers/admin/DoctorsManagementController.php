<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admin\DoctorModel;
use App\Models\clients\Specialty;
use Illuminate\Http\Request;

class DoctorsManagementController extends Controller
{
    public function index()
    {
        $query = DoctorModel::with('specialty');

        if (request('search')) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . request('search') . '%')
                  ->orWhere('email', 'like', '%' . request('search') . '%');
            });
        }

        if (request('specialty_id')) {
            $query->where('specialty_id', request('specialty_id'));
        }

        if (request('status') !== null && request('status') !== '') {
            $query->where('status', request('status'));
        }

        $doctors     = $query->paginate(10);
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
        $request->validate([
            'name'         => 'required|string|max:100',
            'email'        => 'required|email|unique:doctors,email',
            'specialty_id' => 'required|exists:specialties,id',
        ]);

        $data = $request->only([
            'name','email','phone','degree','specialty_id',
            'experience_years','consultation_fee','status',
            'clinic_name','clinic_address','clinic_district','clinic_city',
            'description','working_hours'
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('doctors', 'public');
        }

        if ($request->working_hours) {
            $data['working_hours'] = json_encode($request->working_hours);
        }

        DoctorModel::create($data);

        return redirect()->route('admin.doctors')
            ->with('success', 'Thêm bác sĩ thành công.');
    }

    public function edit($id)
    {
        $doctor      = DoctorModel::findOrFail($id);
        $specialties = Specialty::where('status', 1)->get();
        return view('admin.doctors.form', compact('doctor', 'specialties'));
    }

    public function update(Request $request, $id)
    {
        $doctor = DoctorModel::findOrFail($id);
        $data   = $request->except('avatar');

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('doctors', 'public');
        }

        if ($request->working_hours) {
            $data['working_hours'] = json_encode($request->working_hours);
        }

        $doctor->update($data);

        return redirect()->route('admin.doctors')
            ->with('success', 'Cập nhật bác sĩ thành công.');
    }

    public function delete(Request $request)
    {
        DoctorModel::findOrFail($request->id)->delete();
        return back()->with('success', 'Đã xóa bác sĩ.');
    }
}