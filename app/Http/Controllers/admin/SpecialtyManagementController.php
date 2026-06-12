<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\clients\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SpecialtyManagementController extends Controller
{
    public function index()
    {
        $specialties = Specialty::withCount('doctors')->get();
        return view('admin.specialties', compact('specialties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:specialties,name',
        ]);

        $data = [
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'icon'        => $request->icon,
            'description' => $request->description,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('specialties', 'public');
        }

        Specialty::create($data);

        return back()->with('success', 'Thêm chuyên khoa thành công.');
    }

    public function update(Request $request, $id)
    {
        $specialty = Specialty::findOrFail($id);

        $data = [
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'icon'        => $request->icon,
            'description' => $request->description,
            'status'      => $request->status ?? 1,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('specialties', 'public');
        }

        $specialty->update($data);

        return back()->with('success', 'Cập nhật chuyên khoa thành công.');
    }

    public function delete(Request $request)
    {
        Specialty::findOrFail($request->id)->delete();
        return back()->with('success', 'Đã xóa chuyên khoa.');
    }
}