<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\clients\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminAppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor.specialty']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', fn($q) => $q->where('name', 'like', "%$search%")
                                                     ->orWhere('email', 'like', "%$search%"))
                  ->orWhereHas('doctor', fn($q) => $q->where('name', 'like', "%$search%"));
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
                              ->orderBy('appointment_time', 'desc')
                              ->paginate(15)
                              ->withQueryString();

        $statusCounts = [
            'all'       => Appointment::count(),
            'pending'   => Appointment::where('status', 'pending')->count(),
            'confirmed' => Appointment::where('status', 'confirmed')->count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'cancelled' => Appointment::where('status', 'cancelled')->count(),
        ];

        return view('admin.appointments.index', compact('appointments', 'statusCounts'));
    }

    public function confirm($id)
    {
        Appointment::findOrFail($id)->update(['status' => 'confirmed']);
        return back()->with('success', 'Đã xác nhận lịch hẹn.');
    }

    public function cancel($id)
    {
        Appointment::findOrFail($id)->update(['status' => 'cancelled']);
        return back()->with('success', 'Đã hủy lịch hẹn.');
    }

    public function complete($id)
    {
        Appointment::findOrFail($id)->update(['status' => 'completed']);
        return back()->with('success', 'Đã hoàn thành lịch hẹn.');
    }
}
