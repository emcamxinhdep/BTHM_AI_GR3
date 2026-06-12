{{--
    resources/views/emails/appointment_notification.blade.php
    Biến nhận được từ AppointmentReminderMail:
      $appointment  — Model Appointment (đã load doctor.specialty, patient)
      $emailType    — 'booking' | 'confirm' | 'remind' | 'cancel' | 'custom'
      $customNote   — string|null
--}}
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Thông báo lịch hẹn - DoctorCam</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',Arial,sans-serif;background:#f0f2f5;color:#333}
.wrap{max-width:600px;margin:30px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 16px rgba(0,0,0,.08)}
.header{background:linear-gradient(135deg,#E85D26 0%,#c44a1c 100%);padding:28px 32px;text-align:center}
.header .logo{color:#fff;font-size:22px;font-weight:700;letter-spacing:.5px}
.header .tagline{color:rgba(255,255,255,.8);font-size:12px;margin-top:4px}
.badge{display:inline-block;margin-top:14px;padding:6px 20px;border-radius:20px;font-size:13px;font-weight:600}
.badge-booking{background:#fff3e0;color:#bf6e00}
.badge-confirm{background:#e6f7ec;color:#1a7a3b}
.badge-remind {background:#fff8e1;color:#f57f17}
.badge-cancel {background:#fde8e8;color:#b91c1c}
.badge-custom {background:#e8eeff;color:#1d3fbb}
.body{padding:32px}
.greeting{font-size:15px;line-height:1.8;color:#444;margin-bottom:24px}
.greeting strong{color:#E85D26}
.info-box{background:#fdf6f2;border-left:4px solid #E85D26;border-radius:0 10px 10px 0;padding:20px 24px;margin-bottom:24px}
.info-row{display:flex;align-items:flex-start;padding:9px 0;border-bottom:1px solid #f0e8e3;font-size:14px}
.info-row:last-child{border-bottom:none;padding-bottom:0}
.info-label{width:130px;flex-shrink:0;color:#999;font-size:13px}
.info-value{color:#222;font-weight:600;flex:1}
.note-box{background:#f8f9ff;border:1px solid #dde4ff;border-radius:8px;padding:16px 20px;margin-bottom:24px}
.note-title{font-weight:600;color:#3b4fd9;font-size:13px;margin-bottom:6px}
.note-text{font-size:14px;color:#444;line-height:1.6}
.tips{background:#f9fafb;border-radius:8px;padding:16px 20px;margin-bottom:24px;font-size:13px;color:#555;line-height:1.9}
.tips strong{color:#E85D26;display:block;margin-bottom:4px;font-size:13px}
.cta{text-align:center;margin:28px 0 8px}
.cta a{display:inline-block;background:#E85D26;color:#fff;text-decoration:none;padding:13px 36px;border-radius:8px;font-size:15px;font-weight:600;letter-spacing:.3px}
.divider{border:none;border-top:1px solid #f0ece9;margin:24px 0}
.footer{padding:20px 32px 28px;text-align:center}
.footer p{font-size:12px;color:#aaa;line-height:2}
.footer a{color:#E85D26;text-decoration:none}
@media(max-width:480px){
  .body,.footer{padding:20px}
  .info-label{width:100px}
  .header{padding:20px}
}
</style>
</head>
<body>
<div class="wrap">

{{-- HEADER --}}
<div class="header">
  <div class="logo">🏥 DoctorCam</div>
  <div class="tagline">Hệ thống đặt lịch khám bệnh trực tuyến</div>
  @php
    $badgeMap = [
      'booking' => ['badge-booking', '📋 Đặt lịch thành công'],
      'confirm' => ['badge-confirm', '✅ Lịch hẹn đã được xác nhận'],
      'remind'  => ['badge-remind',  '⏰ Nhắc nhở lịch khám ngày mai'],
      'cancel'  => ['badge-cancel',  '❌ Lịch hẹn đã hủy'],
      'custom'  => ['badge-custom',  '📬 Thông báo lịch hẹn'],
    ];
    [$badgeClass, $badgeText] = $badgeMap[$emailType] ?? $badgeMap['custom'];

    $doctorName   = $appointment->doctor->name ?? 'bác sĩ';
    $specialtyName= $appointment->doctor->specialty->name ?? '—';
    $patientName  = $appointment->patient->name ?? 'Quý bệnh nhân';
    $apptDate     = \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y');
    $apptTime     = substr($appointment->appointment_time, 0, 5);
    $fee          = number_format($appointment->fee ?? 0) . 'đ';
    $payment      = $appointment->payment_method === 'momo' ? 'MoMo' : 'Tiền mặt';
  @endphp
  <span class="badge {{ $badgeClass }}">{{ $badgeText }}</span>
</div>

{{-- BODY --}}
<div class="body">

  {{-- Lời chào --}}
  <p class="greeting">
    Xin chào <strong>{{ $patientName }}</strong>,<br><br>
    @if($emailType === 'booking')
      Bạn đã đặt lịch khám thành công! Chúng tôi sẽ xác nhận lịch hẹn sớm nhất. Vui lòng giữ lịch trống vào thời gian bên dưới.
    @elseif($emailType === 'confirm')
      Lịch hẹn khám của bạn đã được <strong>xác nhận</strong>. Vui lòng đến đúng giờ hẹn và mang theo CMND/CCCD.
    @elseif($emailType === 'remind')
      Đây là email nhắc nhở lịch hẹn khám của bạn <strong>vào ngày mai</strong>. Vui lòng chuẩn bị sẵn sàng và đến trước giờ hẹn 10–15 phút.
    @elseif($emailType === 'cancel')
      Chúng tôi rất tiếc phải thông báo rằng lịch hẹn khám của bạn đã bị <strong>hủy</strong>. Xin lỗi vì sự bất tiện. Bạn có thể đặt lịch mới trên website của chúng tôi.
    @else
      Bạn có thông báo mới về lịch hẹn khám. Vui lòng xem thông tin chi tiết bên dưới.
    @endif
  </p>

  {{-- Thông tin lịch hẹn --}}
  <div class="info-box">
    <div class="info-row">
      <span class="info-label">👤 Bệnh nhân</span>
      <span class="info-value">{{ $patientName }}</span>
    </div>
    <div class="info-row">
      <span class="info-label">🩺 Bác sĩ</span>
      <span class="info-value">BS. {{ $doctorName }}</span>
    </div>
    <div class="info-row">
      <span class="info-label">🏥 Chuyên khoa</span>
      <span class="info-value">{{ $specialtyName }}</span>
    </div>
    <div class="info-row">
      <span class="info-label">📅 Ngày khám</span>
      <span class="info-value">{{ $apptDate }}</span>
    </div>
    <div class="info-row">
      <span class="info-label">⏰ Giờ khám</span>
      <span class="info-value">{{ $apptTime }}</span>
    </div>
    <div class="info-row">
      <span class="info-label">💵 Phí khám</span>
      <span class="info-value">{{ $fee }}</span>
    </div>
    <div class="info-row">
      <span class="info-label">💳 Thanh toán</span>
      <span class="info-value">{{ $payment }}</span>
    </div>
  </div>

  {{-- Ghi chú từ admin --}}
  @if($customNote)
  <div class="note-box">
    <div class="note-title">📝 Ghi chú từ phòng khám</div>
    <div class="note-text">{{ $customNote }}</div>
  </div>
  @endif

  {{-- Lưu ý chuẩn bị (chỉ khi không phải cancel) --}}
  @if($emailType !== 'cancel')
  <div class="tips">
    <strong>📌 Chuẩn bị trước khi đến khám</strong>
    • Mang theo CMND/CCCD hoặc thẻ bảo hiểm y tế (nếu có)<br>
    • Đến trước giờ hẹn 10–15 phút để làm thủ tục đăng ký<br>
    • Nhịn ăn 4–6 giờ nếu có xét nghiệm máu theo yêu cầu<br>
    • Liên hệ hotline <strong>1800 xxxx</strong> nếu cần đổi hoặc hủy lịch
  </div>
  @endif

  {{-- CTA --}}
  @if($emailType !== 'cancel')
  <div class="cta">
    <a href="{{ config('app.url') . '/appointments' }}">Xem lịch hẹn của tôi</a>
  </div>
  @else
  <div class="cta">
    <a href="{{ config('app.url') . '/appointment/book' }}">Đặt lịch khám mới</a>
  </div>
  @endif

</div>

<hr class="divider">

{{-- FOOTER --}}
<div class="footer">
  <p>
    Email này được gửi tự động từ hệ thống <strong>DoctorCam</strong>.<br>
    Vui lòng không trả lời trực tiếp email này.<br>
    Cần hỗ trợ? Liên hệ <a href="mailto:support@doctorcam.vn">support@doctorcam.vn</a>
    hoặc hotline <strong>1800 xxxx</strong>
    <br><br>
    <span style="color:#ccc">© {{ date('Y') }} DoctorCam. All rights reserved.</span>
  </p>
</div>

</div>
</body>
</html>