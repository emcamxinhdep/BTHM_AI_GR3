<?php
// ============================================================
// app/Mail/AppointmentReminderMail.php
// ============================================================
// Class này đã được dùng trong client AppointmentController:
//   use App\Mail\AppointmentReminderMail;
//   Mail::to($patient->email)->send(new AppointmentReminderMail($appointment));
// Đây là class duy nhất cho TẤT CẢ loại email lịch hẹn.
// ============================================================

namespace App\Mail;

use App\Models\clients\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public Appointment $appointment;
    public string $emailType;   // 'booking', 'confirm', 'remind', 'cancel', 'custom'
    public ?string $customNote;

    /**
     * @param Appointment $appointment
     * @param string      $emailType   'booking'|'confirm'|'remind'|'cancel'|'custom'
     * @param string|null $customNote  Ghi chú thêm từ admin (tuỳ chọn)
     */
    public function __construct(
        Appointment $appointment,
        string $emailType = 'booking',
        ?string $customNote = null
    ) {
        $this->appointment = $appointment;
        $this->emailType   = $emailType;
        $this->customNote  = $customNote;
    }

    public function envelope(): Envelope
    {
        $date = \Carbon\Carbon::parse($this->appointment->appointment_date)->format('d/m/Y');

        $subjects = [
            'booking' => "[DoctorCam] Đặt lịch thành công - {$date}",
            'confirm' => "[DoctorCam] Xác nhận lịch hẹn khám - {$date}",
            'remind'  => "[DoctorCam] Nhắc nhở lịch hẹn khám ngày - {$date}",
            'cancel'  => "[DoctorCam] Thông báo hủy lịch hẹn - {$date}",
            'custom'  => "[DoctorCam] Thông báo lịch hẹn khám",
        ];

        return new Envelope(
            subject: $subjects[$this->emailType] ?? $subjects['custom'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'clients.mail.appointment-reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}