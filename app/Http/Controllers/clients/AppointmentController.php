<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\clients\Appointment;
use App\Models\clients\Doctor;
use App\Models\clients\Specialty;
use App\Mail\AppointmentReminderMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with('doctor.specialty')
            ->where('patient_id', session('patient_id'))
            ->orderBy('appointment_date', 'desc')
            ->get();

        return view('clients.appointments', compact('appointments'))
            ->with('title', 'Lịch hẹn của tôi');
    }

    public function create(Request $request)
    {
        $patientId = session('patient_id');

        // Nếu bệnh nhân đang có lịch hẹn chưa hoàn thành (pending/confirmed),
        // chuyển hướng về trang "Lịch hẹn của tôi" kèm thông báo, không cho đặt lịch mới.
        if ($patientId) {
            $pendingAppointment = Appointment::with('doctor')
                ->where('patient_id', $patientId)
                ->whereIn('status', ['pending', 'confirmed'])
                ->orderBy('appointment_date')
                ->orderBy('appointment_time')
                ->first();

            if ($pendingAppointment) {
                $statusLabel = $pendingAppointment->status === 'confirmed' ? 'đã xác nhận' : 'chờ xác nhận';
                $message = sprintf(
                    'Bạn đang có lịch hẹn với %s vào %s lúc %s (%s) chưa hoàn thành. '
                    . 'Vui lòng hoàn thành hoặc hủy lịch hẹn đó trước khi đặt lịch mới.',
                    $pendingAppointment->doctor->name ?? 'bác sĩ',
                    \Carbon\Carbon::parse($pendingAppointment->appointment_date)->format('d/m/Y'),
                    substr($pendingAppointment->appointment_time, 0, 5),
                    $statusLabel
                );

                return redirect()->route('appointments.index')->with('error', $message);
            }
        }

        $specialties = Specialty::where('status', 1)->get();
        $doctors = Doctor::with('specialty')->where('status', 1)->get();

        // Danh sách quận/khu vực để chọn "phòng khám gần bạn"
        // Lấy từ dữ liệu thực tế của các bác sĩ/phòng khám trong DB, loại bỏ giá trị trống/trùng
        $districts = Doctor::where('status', 1)
            ->whereNotNull('clinic_district')
            ->where('clinic_district', '!=', '')
            ->distinct()
            ->pluck('clinic_district')
            ->sort()
            ->values();

        // Nếu chưa có dữ liệu quận nào trong DB, dùng danh sách mặc định (mô phỏng)
        if ($districts->isEmpty()) {
            $districts = collect([
                'Hoàn Kiếm', 'Ba Đình', 'Đống Đa', 'Hai Bà Trưng',
                'Cầu Giấy', 'Thanh Xuân', 'Hà Đông', 'Long Biên',
                'Tây Hồ', 'Hoàng Mai',
            ]);
        }

        // Đề xuất cá nhân hóa dựa trên lịch sử đặt lịch của bệnh nhân
        $recommendedDoctors = collect();
        $patientId = session('patient_id');

        if ($patientId) {
            // Lấy các chuyên khoa bệnh nhân đã từng khám, ưu tiên gần đây nhất
            $recentSpecialtyIds = Appointment::with('doctor')
                ->where('patient_id', $patientId)
                ->orderBy('appointment_date', 'desc')
                ->get()
                ->pluck('doctor.specialty_id')
                ->filter()
                ->unique()
                ->take(3)
                ->values();

            // Các bác sĩ đã khám trước đó (loại trừ khỏi gợi ý để đề xuất lựa chọn mới)
            $visitedDoctorIds = Appointment::where('patient_id', $patientId)
                ->pluck('doctor_id')
                ->unique();

            if ($recentSpecialtyIds->isNotEmpty()) {
                $recommendedDoctors = Doctor::with('specialty')
                    ->where('status', 1)
                    ->whereIn('specialty_id', $recentSpecialtyIds)
                    ->whereNotIn('id', $visitedDoctorIds)
                    ->orderBy('rating', 'desc')
                    ->take(4)
                    ->get();
            }

            // Nếu không đủ gợi ý (hoặc bệnh nhân mới), bổ sung bác sĩ đánh giá cao nhất
            if ($recommendedDoctors->count() < 4) {
                $needed = 4 - $recommendedDoctors->count();
                $existingIds = $recommendedDoctors->pluck('id')
                    ->merge($visitedDoctorIds)
                    ->unique();

                $fillerDoctors = Doctor::with('specialty')
                    ->where('status', 1)
                    ->whereNotIn('id', $existingIds)
                    ->orderBy('rating', 'desc')
                    ->take($needed)
                    ->get();

                $recommendedDoctors = $recommendedDoctors->merge($fillerDoctors);
            }
        } else {
            // Khách chưa đăng nhập: gợi ý bác sĩ đánh giá cao nhất
            $recommendedDoctors = Doctor::with('specialty')
                ->where('status', 1)
                ->orderBy('rating', 'desc')
                ->take(4)
                ->get();
        }

        $selectedDoctor = null;
        $selectedDate = $request->date;
        $availableSlots = [];

        if ($request->doctor_id && $request->date) {
            $selectedDoctor = Doctor::with('specialty')->find($request->doctor_id);
            if ($selectedDoctor) {
                $workingHours = $selectedDoctor->getWorkingHoursArray();
                $dayKey = Carbon::parse($request->date)->format('D'); // Mon, Tue, ...
                if (isset($workingHours[$dayKey])) {
                    $rangeStr = $workingHours[$dayKey];
                    $ranges = explode(',', $rangeStr);
                    $allSlots = [];
                    foreach ($ranges as $range) {
                        $parts = explode('-', trim($range));
                        if (count($parts) != 2) continue;
                        $startH = (int)$parts[0];
                        $endH = (int)$parts[1];
                        $current = Carbon::parse($request->date)->setTime($startH, 0);
                        $end = Carbon::parse($request->date)->setTime($endH, 0);
                        while ($current->lt($end)) {
                            $allSlots[] = $current->format('H:i');
                            $current->addMinutes(30);
                        }
                    }
                    $bookedSlots = Appointment::where('doctor_id', $request->doctor_id)
                        ->where('appointment_date', $request->date)
                        ->whereIn('status', ['pending', 'confirmed'])
                        ->pluck('appointment_time')
                        ->map(fn($t) => substr($t, 0, 5))
                        ->toArray();
                    foreach ($allSlots as $slot) {
                        $availableSlots[] = [
                            'time' => $slot,
                            'available' => !in_array($slot, $bookedSlots)
                        ];
                    }
                }
            }
        }

        return view('clients.booking', compact(
            'specialties',
            'doctors',
            'districts',
            'recommendedDoctors',
            'selectedDoctor',
            'selectedDate',
            'availableSlots'
        ))->with('title', 'Đặt lịch khám');
    }

    public function checkSlots(Request $request)
    {
        \Log::info('checkSlots called', $request->all());

        $doctorId = $request->doctor_id;
        $date = $request->date;
        $doctor = Doctor::find($doctorId);

        if (!$doctor) {
            return response()->json(['available' => false, 'slots' => [], 'message' => 'Không tìm thấy bác sĩ']);
        }

        $workingHours = $doctor->getWorkingHoursArray();
        $dayKey = Carbon::parse($date)->format('D'); // "Mon", "Tue", ...

        if (empty($workingHours)) {
            return response()->json(['available' => false, 'message' => 'Bác sĩ chưa cập nhật lịch làm việc', 'slots' => []]);
        }

        if (!isset($workingHours[$dayKey])) {
            $dayNames = ['Mon' => 'Thứ 2', 'Tue' => 'Thứ 3', 'Wed' => 'Thứ 4', 'Thu' => 'Thứ 5', 'Fri' => 'Thứ 6', 'Sat' => 'Thứ 7', 'Sun' => 'Chủ nhật'];
            $availableDays = array_map(fn($d) => $dayNames[$d] ?? $d, array_keys($workingHours));
            $message = 'Bác sĩ chỉ làm việc các ngày: ' . implode(', ', $availableDays);
            return response()->json(['available' => false, 'message' => $message, 'slots' => []]);
        }

        $rangeStr = $workingHours[$dayKey];
        $ranges = explode(',', $rangeStr);
        $allSlots = [];

        foreach ($ranges as $range) {
            $parts = explode('-', trim($range));
            if (count($parts) != 2) continue;
            $startH = (int)$parts[0];
            $endH = (int)$parts[1];
            $current = Carbon::parse($date)->setTime($startH, 0);
            $end = Carbon::parse($date)->setTime($endH, 0);

            while ($current->lt($end)) {
                $allSlots[] = $current->format('H:i');
                $current->addMinutes(30);
            }
        }

        if (empty($allSlots)) {
            return response()->json(['available' => false, 'message' => 'Khung giờ không hợp lệ', 'slots' => []]);
        }

        $bookedSlots = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('appointment_time')
            ->map(fn($t) => substr($t, 0, 5))
            ->toArray();

        $slots = array_map(fn($s) => [
            'time' => $s,
            'available' => !in_array($s, $bookedSlots)
        ], $allSlots);

        return response()->json(['available' => true, 'slots' => $slots]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'doctor_id'        => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'symptoms'         => 'nullable|string|max:500',
            // ✅ Thêm validate Bước 5
            'fullname'         => 'required|string|max:255',
            'email'            => 'required|email|max:255',
            'phone'            => 'required|string|max:20',
            'note'             => 'nullable|string|max:500',
        ]);

        $patientId = session('patient_id');

        // Kiểm tra bệnh nhân có lịch hẹn nào chưa hoàn thành (pending/confirmed) không.
        // Nếu có -> không cho đặt lịch mới, yêu cầu hoàn thành/hủy lịch hiện tại trước.
        if ($patientId) {
            $pendingAppointment = Appointment::with('doctor.specialty')
                ->where('patient_id', $patientId)
                ->whereIn('status', ['pending', 'confirmed'])
                ->orderBy('appointment_date')
                ->orderBy('appointment_time')
                ->first();

            if ($pendingAppointment) {
                $statusLabel = $pendingAppointment->status === 'confirmed' ? 'đã xác nhận' : 'chờ xác nhận';
                $message = sprintf(
                    'Bạn đang có lịch hẹn với %s vào %s lúc %s (%s) chưa hoàn thành. '
                    . 'Vui lòng hoàn thành hoặc hủy lịch hẹn đó trước khi đặt lịch mới.',
                    $pendingAppointment->doctor->name ?? 'bác sĩ',
                    \Carbon\Carbon::parse($pendingAppointment->appointment_date)->format('d/m/Y'),
                    substr($pendingAppointment->appointment_time, 0, 5),
                    $statusLabel
                );

                return back()
                    ->with('error', $message)
                    ->withInput();
            }
        }

        $conflict = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($conflict) {
            $alternatives = $this->getSuggestedSlots(
                $request->doctor_id,
                $request->appointment_date,
                $request->appointment_time
            );
            return back()
                ->with('conflict', true)
                ->with('alternatives', $alternatives)
                ->withInput();
        }

        $doctor = Doctor::findOrFail($request->doctor_id);
        $appointment = Appointment::create([
            'patient_id'       => session('patient_id'),
            'doctor_id'        => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'symptoms'         => $request->symptoms,
            'note'             => $request->note,
            'fee'              => $doctor->consultation_fee,
            'payment_method'   => $request->payment_method ?? 'cash',
            'status'           => 'pending',
        ]);

        $appointment->load(['doctor.specialty', 'patient']);

        try {
            // ✅ Dùng email người dùng nhập trong form, fallback về email trong DB
            $emailToSend = $request->email 
                ?? $appointment->patient->email 
                ?? null;

            if ($emailToSend) {
                Mail::to($emailToSend)->send(new AppointmentReminderMail($appointment));
            }
        } catch (\Exception $e) {
            \Log::error('Mail error: ' . $e->getMessage());
        }

        return redirect()->route('appointments.index')
            ->with('success', 'Đặt lịch thành công! Chúng tôi sẽ xác nhận sớm.');
    }

    public function cancel($id)
    {
        Appointment::where('id', $id)
            ->where('patient_id', session('patient_id'))
            ->update(['status' => 'cancelled']);

        return back()->with('success', 'Đã hủy lịch hẹn.');
    }

    private function getSuggestedSlots($doctorId, $date, $time): array
    {
        $suggestions = [];

        foreach ([30, 60, 90, 120] as $offset) {
            $newTime = Carbon::parse($date . ' ' . $time)->addMinutes($offset);
            $taken = Appointment::where('doctor_id', $doctorId)
                ->where('appointment_date', $date)
                ->where('appointment_time', $newTime->format('H:i'))
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            if (!$taken) {
                $suggestions[] = [
                    'date'  => $date,
                    'time'  => $newTime->format('H:i'),
                    'label' => 'Hôm nay lúc ' . $newTime->format('H:i')
                ];
            }
            if (count($suggestions) >= 3) break;
        }

        if (count($suggestions) < 3) {
            $tomorrow = Carbon::parse($date)->addDay()->format('Y-m-d');
            $suggestions[] = [
                'date'  => $tomorrow,
                'time'  => $time,
                'label' => 'Ngày mai lúc ' . $time
            ];
        }

        return $suggestions;
    }
}