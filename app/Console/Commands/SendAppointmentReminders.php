<?php
// ============================================================
// app/Console/Commands/SendAppointmentReminders.php
// ============================================================

namespace App\Console\Commands;

use App\Mail\AppointmentReminderMail;
use App\Models\clients\Appointment;   // ← namespace đúng với project
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminders extends Command
{
    protected $signature   = 'appointments:send-reminders';
    protected $description = 'Gửi email nhắc nhở lịch hẹn trước 1 ngày (chạy 08:00 mỗi ngày)';

    public function handle(): void
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        // Lấy lịch hẹn ngày mai chưa gửi nhắc nhở (reminder_sent = false/0)
        $appointments = Appointment::with(['patient', 'doctor.specialty'])
            ->whereDate('appointment_date', $tomorrow)
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('reminder_sent', false)   // tránh gửi trùng
            ->get();

        if ($appointments->isEmpty()) {
            $this->info('Không có lịch hẹn nào cần nhắc nhở ngày mai.');
            return;
        }

        $this->info("Tìm thấy {$appointments->count()} lịch hẹn cần nhắc nhở...");

        $sent   = 0;
        $errors = 0;

        foreach ($appointments as $appointment) {
            $email = $appointment->patient->email ?? null;

            if (!$email) {
                $this->warn("Bỏ qua #{$appointment->id}: bệnh nhân không có email.");
                continue;
            }

            try {
                Mail::to($email)->send(
                    new AppointmentReminderMail($appointment, 'remind')
                );

                // Đánh dấu đã gửi để không gửi lại
                $appointment->update(['reminder_sent' => true]);

                $sent++;
                $this->info("✅ Gửi đến {$email} (Lịch #{$appointment->id} - {$appointment->patient->name})");
            } catch (\Exception $e) {
                $errors++;
                $this->error("❌ Lỗi #{$appointment->id}: " . $e->getMessage());
            }
        }

        $this->info("Hoàn tất: {$sent} email đã gửi, {$errors} lỗi.");
    }
}