<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác nhận đặt lịch khám</title>
</head>
<body style="font-family: Arial, sans-serif; background-color:#f4f6f8; margin:0; padding:0;">
    <div style="max-width:600px; margin:0 auto; background:#ffffff; padding:24px; border-radius:8px;">

        <div style="text-align:center; margin-bottom:20px;">
            <h2 style="color:#ff6f3c; margin:0;">DoctorCam</h2>
            <p style="color:#888; margin:4px 0 0;">Đặt lịch khám sức khỏe trực tuyến</p>
        </div>

        <h3 style="color:#333;">Xin chào{{ isset($appointment->patient) ? ', ' . $appointment->patient->fullname : '' }}!</h3>

        <p>Bạn đã đặt lịch khám thành công với thông tin sau:</p>

        <table style="width:100%; border-collapse: collapse; margin: 16px 0;">
            <tr>
                <td style="padding:8px; border-bottom:1px solid #eee; font-weight:bold; width:40%;">Bác sĩ</td>
                <td style="padding:8px; border-bottom:1px solid #eee;">
                    {{ $appointment->doctor->name ?? '' }}
                    @if(!empty($appointment->doctor->specialty))
                        ({{ $appointment->doctor->specialty->name }})
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding:8px; border-bottom:1px solid #eee; font-weight:bold;">Phòng khám</td>
                <td style="padding:8px; border-bottom:1px solid #eee;">{{ $appointment->doctor->clinic_name ?? '' }}</td>
            </tr>
            <tr>
                <td style="padding:8px; border-bottom:1px solid #eee; font-weight:bold;">Địa chỉ</td>
                <td style="padding:8px; border-bottom:1px solid #eee;">{{ $appointment->doctor->clinic_address ?? '' }}</td>
            </tr>
            <tr>
                <td style="padding:8px; border-bottom:1px solid #eee; font-weight:bold;">Ngày khám</td>
                <td style="padding:8px; border-bottom:1px solid #eee;">
                    {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}
                </td>
            </tr>
            <tr>
                <td style="padding:8px; border-bottom:1px solid #eee; font-weight:bold;">Giờ khám</td>
                <td style="padding:8px; border-bottom:1px solid #eee;">
                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}
                </td>
            </tr>
            @if(!empty($appointment->symptoms))
            <tr>
                <td style="padding:8px; border-bottom:1px solid #eee; font-weight:bold;">Triệu chứng</td>
                <td style="padding:8px; border-bottom:1px solid #eee;">{{ $appointment->symptoms }}</td>
            </tr>
            @endif
            <tr>
                <td style="padding:8px; border-bottom:1px solid #eee; font-weight:bold;">Phí khám</td>
                <td style="padding:8px; border-bottom:1px solid #eee;">{{ number_format($appointment->fee) }} VNĐ</td>
            </tr>
            <tr>
                <td style="padding:8px; border-bottom:1px solid #eee; font-weight:bold;">Phương thức thanh toán</td>
                <td style="padding:8px; border-bottom:1px solid #eee;">
                    {{ $appointment->payment_method === 'momo' ? 'Thanh toán qua MoMo' : 'Thanh toán tại phòng khám' }}
                </td>
            </tr>
            <tr>
                <td style="padding:8px; font-weight:bold;">Trạng thái</td>
                <td style="padding:8px;">
                    <span style="background:#fff3cd; color:#856404; padding:2px 8px; border-radius:4px;">
                        Chờ xác nhận
                    </span>
                </td>
            </tr>
        </table>

        <p style="color:#555;">
            Chúng tôi sẽ liên hệ xác nhận lịch hẹn với bạn trong thời gian sớm nhất.
            Vui lòng đến phòng khám trước giờ hẹn khoảng 10–15 phút.
        </p>

        <p style="margin-top:24px; color:#999; font-size:13px; text-align:center;">
            Đây là email tự động, vui lòng không phản hồi trực tiếp email này.<br>
            © {{ date('Y') }} DoctorCam. Mọi quyền được bảo lưu.
        </p>
    </div>
</body>
</html>