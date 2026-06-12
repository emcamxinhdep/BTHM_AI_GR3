<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\clients\Appointment;
use App\Models\clients\Doctor;
use App\Models\clients\Patient;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        $stats = [
            'total_appointments' => Appointment::count(),
            'today_appointments' => Appointment::whereDate('appointment_date', $today)->count(),
            'pending'            => Appointment::where('status', 'pending')->count(),
            'confirmed'          => Appointment::where('status', 'confirmed')->count(),
            'completed'          => Appointment::where('status', 'completed')->count(),
            'cancelled'          => Appointment::where('status', 'cancelled')->count(),
            'total_patients'     => Patient::count(),
            'total_doctors'      => Doctor::count(),
            'revenue_today'      => Appointment::whereDate('appointment_date', $today)
                                        ->where('status', 'completed')->sum('fee'),
            'revenue_month'      => Appointment::whereMonth('appointment_date', Carbon::now()->month)
                                        ->where('status', 'completed')->sum('fee'),
        ];

        $pendingAppointments = Appointment::with(['patient', 'doctor.specialty'])
            ->where('status', 'pending')
            ->orderBy('appointment_date')->orderBy('appointment_time')
            ->take(5)->get();

        $todayAppointments = Appointment::with(['patient', 'doctor.specialty'])
            ->whereDate('appointment_date', $today)
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('appointment_time')->get();

        $revenueChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $revenueChart[] = [
                'date'    => $date->format('d/m'),
                'revenue' => Appointment::whereDate('appointment_date', $date->toDateString())
                                ->where('status', 'completed')->sum('fee'),
                'count'   => Appointment::whereDate('appointment_date', $date->toDateString())->count(),
            ];
        }

        $topDoctors = Doctor::withCount('appointments')
            ->orderBy('appointments_count', 'desc')->take(5)->get();

        return view('admin.dashboard', compact(
            'stats', 'pendingAppointments', 'todayAppointments', 'revenueChart', 'topDoctors'
        ));
    }
}