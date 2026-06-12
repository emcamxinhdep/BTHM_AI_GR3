<?php
// ============================================================
// app/Http/Controllers/admin/AppointmentManagementController.php
// ============================================================
// MERGE các method mới vào controller admin hiện tại của bạn.
// Giữ nguyên các method: index(), confirm(), finish(), cancel()
// Chỉ thêm method: sendNotification() và sửa confirm()/cancel()
// để tự động gửi email.
// ============================================================

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Mail\AppointmentReminderMail;
use App\Models\clients\Appointment;   // ← namespace đúng
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AppointmentManagementController extends Controller
{
    // ---------------------------------------------------------------- index
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor.specialty']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('patient', fn($p) => $p->where('name', 'like', "%{$s}%")
                                                      ->orWhere('email', 'like', "%{$s}%"))
                  ->orWhereHas('doctor',  fn($d) => $d->where('name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
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

    // ---------------------------------------------------------------- confirm
    // Xác nhận lịch hẹn + tự động gửi email "Đã xác nhận"
    public function confirm($id)
    {
        $appointment = Appointment::with(['patient', 'doctor.specialty'])->findOrFail($id);
        $appointment->update(['status' => 'confirmed']);

        $this->trySendMail($appointment, 'confirm');

        return back()->with('success', 'Đã xác nhận lịch hẹn và gửi email thông báo đến bệnh nhân.');
    }

    // ---------------------------------------------------------------- finish
    public function finish($id)
    {
        Appointment::findOrFail($id)->update(['status' => 'completed']);

        return back()->with('success', 'Đã đánh dấu lịch hẹn hoàn thành.');
    }

    // ---------------------------------------------------------------- cancel
    // Hủy lịch hẹn + tự động gửi email "Đã hủy"
    public function cancel($id)
    {
        $appointment = Appointment::with(['patient', 'doctor.specialty'])->findOrFail($id);
        $appointment->update(['status' => 'cancelled']);

        $this->trySendMail($appointment, 'cancel');

        return back()->with('success', 'Đã hủy lịch hẹn và gửi thông báo đến bệnh nhân.');
    }

    // ---------------------------------------------------------------- sendNotification (gửi thủ công từ modal)
    public function sendNotification(Request $request, $id)
    {
        $request->validate([
            'email_type'  => 'required|in:booking,confirm,remind,cancel,custom',
            'custom_note' => 'nullable|string|max:500',
        ]);

        $appointment = Appointment::with(['patient', 'doctor.specialty'])->findOrFail($id);
        $email       = $appointment->patient->email ?? null;

        if (!$email) {
            return back()->with('error', 'Bệnh nhân không có địa chỉ email.');
        }

        try {
            Mail::to($email)->send(
                new AppointmentReminderMail($appointment, $request->email_type, $request->custom_note)
            );

            $label = $this->typeLabel($request->email_type);
            return back()->with('success', "Email \"{$label}\" đã gửi đến {$email}.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gửi email thất bại: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------- helpers
    private function trySendMail(Appointment $appointment, string $type): void
    {
        $email = $appointment->patient->email ?? null;
        if (!$email) return;

        try {
            Mail::to($email)->send(new AppointmentReminderMail($appointment, $request->email_type, $request->custom_note));
        } catch (\Exception $e) {
            \Log::warning("Gửi email lịch #{$appointment->id} thất bại: " . $e->getMessage());
        }
    }

    private function typeLabel(string $type): string
    {
        return match($type) {
            'booking' => 'Đặt lịch thành công',
            'confirm' => 'Xác nhận lịch hẹn',
            'remind'  => 'Nhắc nhở lịch hẹn',
            'cancel'  => 'Hủy lịch hẹn',
            default   => 'Thông báo tùy chỉnh',
        };
    }
}