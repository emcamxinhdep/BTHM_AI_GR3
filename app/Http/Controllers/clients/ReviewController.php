<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\clients\Review;
use App\Models\clients\Doctor;
use App\Models\clients\Appointment;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id'      => 'required|exists:doctors,id',
            'appointment_id' => 'required|exists:appointments,id',
            'rating'         => 'required|integer|min:1|max:5',
            'comment'        => 'nullable|string|max:500',
        ]);

        // Kiểm tra appointment thuộc về patient đang login
        $appointment = Appointment::where('id', $request->appointment_id)
            ->where('patient_id', session('patient_id'))
            ->where('status', 'completed')
            ->firstOrFail();

        // Kiểm tra đã review chưa
        $exists = Review::where('appointment_id', $request->appointment_id)
            ->where('patient_id', session('patient_id'))
            ->exists();

        if ($exists) {
            return back()->with('error', 'Bạn đã đánh giá lịch hẹn này rồi.');
        }

        Review::create([
            'patient_id'     => session('patient_id'),
            'doctor_id'      => $request->doctor_id,
            'appointment_id' => $request->appointment_id,
            'rating'         => $request->rating,
            'comment'        => $request->comment,
        ]);

        // Cập nhật rating trung bình của bác sĩ
        $doctor = Doctor::find($request->doctor_id);
        $avg = Review::where('doctor_id', $request->doctor_id)->avg('rating');
        $count = Review::where('doctor_id', $request->doctor_id)->count();
        $doctor->update([
            'rating'        => round($avg, 2),
            'total_reviews' => $count,
        ]);

        return back()->with('success', 'Cảm ơn bạn đã đánh giá!');
    }
}