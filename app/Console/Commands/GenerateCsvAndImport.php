<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

class GenerateCsvAndImport extends Command
{
    protected $signature   = 'csv:generate-and-import';
    protected $description = 'Tạo file CSV dữ liệu mẫu và import vào database';

    private string $csvDir;

    public function handle(): void
    {
        $this->csvDir = database_path('data');
        File::ensureDirectoryExists($this->csvDir);

        $this->info('📁 Thư mục CSV: ' . $this->csvDir);
        $this->newLine();

        $this->generateSpecialtiesCsv();
        $this->generateDoctorsCsv();
        $this->generatePatientsCsv();
        $this->generateAppointmentsCsv();
        $this->generatePaymentsCsv();
        $this->generateReviewsCsv();

        $this->newLine();
        $this->info('📥 Bắt đầu import vào database...');
        $this->newLine();

        $this->importSpecialties();
        $this->importDoctors();
        $this->importPatients();
        $this->importAppointments();
        $this->importPayments();
        $this->importReviews();

        $this->newLine();
        $this->info('✅ Hoàn tất!');
    }

    // ==================== TẠO FILE CSV ====================

    private function generateSpecialtiesCsv(): void
    {
        $rows = [
            ['id', 'name', 'slug', 'icon', 'image', 'description', 'status'],
            [1, 'Nội khoa', 'noi-khoa', 'fas fa-lungs', 'specialties/noi-khoa.jpg', 'Điều trị bệnh nội tạng, hô hấp, tiêu hóa', 1],
            [2, 'Nhi khoa', 'nhi-khoa', 'fas fa-child', 'specialties/nhi-khoa.jpg', 'Chăm sóc sức khỏe trẻ em từ 0-16 tuổi', 1],
            [3, 'Da liễu', 'da-lieu', 'fas fa-allergies', 'specialties/da-lieu.jpg', 'Bệnh lý về da, tóc, móng', 1],
            [4, 'Tim mạch', 'tim-mach', 'fas fa-heartbeat', 'specialties/tim-mach.jpg', 'Bệnh tim, mạch máu, tăng huyết áp', 1],
            [5, 'Cơ xương khớp', 'co-xuong-khop', 'fas fa-bone', 'specialties/co-xuong-khop.jpg', 'Thoái hóa khớp, đau cột sống', 1],
            [6, 'Tai Mũi Họng', 'tai-mui-hong', 'fas fa-ear-deaf', 'specialties/tai-mui-hong.jpg', 'Viêm họng, viêm xoang, ù tai', 1],
            [7, 'Mắt', 'mat', 'fas fa-eye', 'specialties/mat.jpg', 'Cận thị, đục thủy tinh thể', 1],
            [8, 'Tâm thần', 'tam-than', 'fas fa-brain', 'specialties/tam-than.jpg', 'Trầm cảm, lo âu, mất ngủ', 1],
            [9, 'Nội tiết', 'noi-tiet', 'fas fa-thyroid', 'specialties/noi-tiet.jpg', 'Tiểu đường, tuyến giáp, rối loạn nội tiết', 1],
            [10, 'Tiêu hóa', 'tieu-hoa', 'fas fa-stomach', 'specialties/tieu-hoa.jpg', 'Đau dạ dày, viêm đại tràng', 1],
        ];
        $this->writeCsv('specialties.csv', $rows);
        $this->info('  ✅ specialties.csv — ' . (count($rows) - 1) . ' chuyên khoa');
    }

    private function generateDoctorsCsv(): void
    {
        $rows = [
            ['id', 'specialty_id', 'name', 'email', 'phone', 'avatar', 'degree', 'clinic_name', 'clinic_address', 'clinic_district', 'clinic_city', 'latitude', 'longitude', 'experience_years', 'consultation_fee', 'rating', 'total_reviews', 'description', 'working_hours', 'status'],
            [1, 1, 'BS. Nguyễn Văn An', 'bs.an@medcare.vn', '0901234001', 'doctors/an.jpg', 'ThS.BS', 'Phòng Khám Đa Khoa MEDCARE', '123 Trần Hưng Đạo, P. Cầu Ông Lãnh', 'Quận 1', 'TP.HCM', 10.776900, 106.700900, 15, 200000, 4.8, 42, 'Chuyên gia nội khoa 15 năm kinh nghiệm', json_encode(['Mon' => '8-12,14-17', 'Tue' => '8-12,14-17', 'Wed' => '8-12,14-17', 'Thu' => '8-12,14-17', 'Fri' => '8-12,14-17']), 1],
            [2, 2, 'BS. Trần Thị Bình', 'bs.binh@medcare.vn', '0901234002', 'doctors/binh.jpg', 'BS.CKII', 'Phòng Khám Đa Khoa MEDCARE', '123 Trần Hưng Đạo, P. Cầu Ông Lãnh', 'Quận 1', 'TP.HCM', 10.776900, 106.700900, 12, 180000, 4.7, 38, 'Bác sĩ Nhi khoa - nguyên trưởng khoa Nhi BV Nhi Đồng 1', json_encode(['Mon' => '8-12', 'Wed' => '8-12,14-17', 'Fri' => '8-12,14-17', 'Sat' => '8-12']), 1],
            [3, 3, 'BS. Lê Minh Cường', 'bs.cuong@healthplus.vn', '0901234003', 'doctors/cuong.jpg', 'BS', 'Phòng Khám HealthPlus', '456 Nguyễn Văn Cừ, P. 1', 'Quận 5', 'TP.HCM', 10.752000, 106.683000, 10, 150000, 4.6, 27, 'Chuyên điều trị mụn, nám, viêm da cơ địa', json_encode(['Tue' => '14-17', 'Thu' => '14-17', 'Sat' => '8-12']), 1],
            [4, 4, 'BS. Phạm Thị Dung', 'bs.dung@tamanh.vn', '0901234004', 'doctors/dung.jpg', 'TS.BS', 'Phòng Khám Chuyên Khoa Tâm Anh', '321 Hoàng Văn Thụ, P.2', 'Tân Bình', 'TP.HCM', 10.801000, 106.665000, 20, 350000, 4.9, 103, 'Tiến sĩ Tim mạch - Giảng viên ĐH Y Dược TP.HCM', json_encode(['Mon' => '8-12', 'Tue' => '8-12', 'Wed' => '8-12', 'Thu' => '8-12']), 1],
            [5, 5, 'BS. Hoàng Văn Em', 'bs.em@care.vn', '0901234005', 'doctors/em.jpg', 'BS.CKII', 'Phòng Khám Nhi Đồng Care', '789 Lý Thường Kiệt, P. 7', 'Quận 10', 'TP.HCM', 10.772000, 106.668000, 8, 160000, 4.5, 19, 'Chuyên cơ xương khớp, thoát vị đĩa đệm', json_encode(['Mon' => '14-17', 'Tue' => '8-12', 'Thu' => '14-17', 'Fri' => '8-12']), 1],
            [6, 6, 'BS. Võ Thị Phương', 'bs.phuong@viet.vn', '0901234006', 'doctors/phuong.jpg', 'BS', 'Phòng Khám Sức Khỏe Việt', '987 CMT8, P. 5', 'Quận 3', 'TP.HCM', 10.785000, 106.689000, 14, 170000, 4.8, 55, 'Chuyên khoa Tai Mũi Họng - Phẫu thuật nội soi xoang', json_encode(['Wed' => '8-12,14-17', 'Thu' => '8-12,14-17', 'Fri' => '8-12', 'Sat' => '8-12']), 1],
            [7, 7, 'BS. Đặng Minh Giang', 'bs.giang@ankhang.vn', '0901234007', 'doctors/giang.jpg', 'ThS.BS', 'Bệnh Viện Đa Khoa An Khang', '147 Võ Văn Tần, P.6', 'Quận 3', 'TP.HCM', 10.778000, 106.692000, 11, 190000, 4.7, 32, 'Bác sĩ Mắt - Phẫu thuật Lasik', json_encode(['Mon' => '8-12', 'Tue' => '8-12', 'Wed' => '14-17', 'Fri' => '8-12']), 1],
            [8, 8, 'BS. Ngô Thị Hoa', 'bs.hoa@tamanh.vn', '0901234008', 'doctors/hoa.jpg', 'BS.CKII', 'Phòng Khám Chuyên Khoa Tâm Anh', '321 Hoàng Văn Thụ, P.2', 'Tân Bình', 'TP.HCM', 10.801000, 106.665000, 9, 250000, 4.8, 47, 'Chuyên trị liệu tâm lý, rối loạn lo âu', json_encode(['Tue' => '14-17', 'Thu' => '14-17', 'Sat' => '8-12']), 1],
            [9, 9, 'BS. Lý Văn Khoa', 'bs.khoa@quocte.vn', '0901234009', 'doctors/khoa.jpg', 'TS.BS', 'Phòng Khám Đa Khoa Quốc Tế', '654 Điện Biên Phủ, P.22', 'Bình Thạnh', 'TP.HCM', 10.810000, 106.715000, 16, 220000, 4.9, 78, 'Chuyên gia nội tiết - Điều trị tiểu đường, bướu giáp', json_encode(['Mon' => '8-12,14-17', 'Wed' => '8-12,14-17', 'Thu' => '8-12', 'Fri' => '8-12']), 1],
            [10, 10, 'BS. Trịnh Thị Lan', 'bs.lan@hormone.vn', '0901234010', 'doctors/lan.jpg', 'BS', 'Phòng Khám Nội Tiết Hormone', '258 Nam Kỳ Khởi Nghĩa, P.8', 'Quận 3', 'TP.HCM', 10.774000, 106.696000, 13, 180000, 4.7, 41, 'Chuyên khoa Tiêu hóa - Nội soi dạ dày đại tràng', json_encode(['Mon' => '8-12', 'Tue' => '14-17', 'Thu' => '14-17', 'Sat' => '8-12']), 1],
        ];
        $this->writeCsv('doctors.csv', $rows);
        $this->info('  ✅ doctors.csv — ' . (count($rows) - 1) . ' bác sĩ');
    }

    private function generatePatientsCsv(): void
    {
        $rows = [
            ['id', 'name', 'email', 'password', 'phone', 'avatar', 'birthday', 'gender', 'address', 'district', 'city', 'latitude', 'longitude', 'blood_type', 'medical_history', 'status'],
            [1, 'Nguyễn Văn An', 'an.nguyen@email.com', Hash::make('123456'), '0912345671', 'patients/an.jpg', '1985-03-12', 'male', '12 Lê Lợi, P. Bến Nghé', 'Quận 1', 'TP.HCM', 10.778000, 106.700000, 'O+', 'Tiền sử viêm xoang', 1],
            [2, 'Trần Thị Bình', 'binh.tran@email.com', Hash::make('123456'), '0912345672', 'patients/binh.jpg', '1990-07-25', 'female', '45 Nguyễn Thị Minh Khai, P. Đa Kao', 'Quận 1', 'TP.HCM', 10.779000, 106.701000, 'A+', 'Hen suyễn', 1],
            [3, 'Lê Văn Cường', 'cuong.le@email.com', Hash::make('123456'), '0912345673', 'patients/cuong.jpg', '1978-11-02', 'male', '78 CMT8, P. 5', 'Quận 10', 'TP.HCM', 10.772000, 106.667000, 'B+', 'Tăng huyết áp', 1],
            [4, 'Phạm Thị Dung', 'dung.pham@email.com', Hash::make('123456'), '0912345674', 'patients/dung.jpg', '1995-05-18', 'female', '123 Nguyễn Văn Cừ, P.1', 'Quận 5', 'TP.HCM', 10.752000, 106.682000, 'AB-', null, 1],
            [5, 'Hoàng Văn Em', 'em.hoang@email.com', Hash::make('123456'), '0912345675', 'patients/em.jpg', '1982-09-30', 'male', '456 Lý Thường Kiệt, P.7', 'Quận 10', 'TP.HCM', 10.771000, 106.669000, 'O-', 'Đau dạ dày mãn tính', 1],
            [6, 'Võ Thị Phương', 'phuong.vo@email.com', Hash::make('123456'), '0912345676', 'patients/phuong.jpg', '1992-12-12', 'female', '789 Hoàng Văn Thụ, P.2', 'Tân Bình', 'TP.HCM', 10.800000, 106.665000, 'A-', null, 1],
            [7, 'Đặng Minh Giang', 'giang.dang@email.com', Hash::make('123456'), '0912345677', 'patients/giang.jpg', '1988-03-22', 'male', '147 Võ Văn Tần, P.6', 'Quận 3', 'TP.HCM', 10.777000, 106.693000, 'B-', 'Viêm khớp gối', 1],
            [8, 'Ngô Thị Hoa', 'hoa.ngo@email.com', Hash::make('123456'), '0912345678', 'patients/hoa.jpg', '1993-08-05', 'female', '258 Nam Kỳ Khởi Nghĩa, P.8', 'Quận 3', 'TP.HCM', 10.774000, 106.696000, 'AB+', null, 1],
            [9, 'Lý Văn Khoa', 'khoa.ly@email.com', Hash::make('123456'), '0912345679', 'patients/khoa.jpg', '1980-01-15', 'male', '369 Điện Biên Phủ, P.22', 'Bình Thạnh', 'TP.HCM', 10.809000, 106.714000, 'O+', 'Tiểu đường type 2', 1],
            [10, 'Trịnh Thị Lan', 'lan.trinh@email.com', Hash::make('123456'), '0912345680', 'patients/lan.jpg', '1991-06-09', 'female', '121 Trần Hưng Đạo, P. Cầu Ông Lãnh', 'Quận 1', 'TP.HCM', 10.776000, 106.701000, 'A+', null, 1],
        ];
        $this->writeCsv('patients.csv', $rows);
        $this->info('  ✅ patients.csv — ' . (count($rows) - 1) . ' bệnh nhân');
    }

    private function generateAppointmentsCsv(): void
    {
        $rows = [
            ['id', 'patient_id', 'doctor_id', 'appointment_date', 'appointment_time', 'symptoms', 'note', 'diagnosis', 'fee', 'payment_method', 'payment_status', 'payment_transaction_id', 'status', 'reminder_sent'],
            [1, 1, 1, '2025-06-15', '09:00:00', 'Ho, sốt 38°C, đau rát họng', 'Đã uống paracetamol', 'Viêm họng cấp', 200000, 'momo', 'paid', 'MOMO_ABC123', 'completed', true],
            [2, 1, 3, '2025-06-15', '10:30:00', 'Nổi mẩn đỏ ở mặt và tay', 'Ngứa nhiều về đêm', 'Dị ứng mỹ phẩm', 150000, 'cash', 'unpaid', null, 'confirmed', false],
            [3, 2, 4, '2025-06-16', '14:00:00', 'Đau tức ngực, khó thở khi gắng sức', 'Tiền sử tăng huyết áp', 'Rối loạn nhịp tim', 350000, 'momo', 'paid', 'MOMO_DEF456', 'confirmed', true],
            [4, 3, 2, '2025-06-17', '08:30:00', 'Sốt cao 39°C, ho có đờm xanh', 'Chưa khám ở đâu', 'Viêm phế quản', 180000, 'momo', 'paid', 'MOMO_GHI789', 'completed', true],
            [5, 4, 9, '2025-06-18', '15:30:00', 'Mệt mỏi, khát nước, đi tiểu nhiều', 'Đường huyết cao 180mg/dL', 'Tiểu đường type 2', 220000, 'cash', 'unpaid', null, 'pending', false],
            [6, 5, 5, '2025-06-19', '11:00:00', 'Đau lưng dưới, tê bì chân trái', 'Hỏi ý kiến hủy lịch', 'Thoái hóa cột sống', 160000, 'momo', 'paid', 'MOMO_JKL012', 'cancelled', true],
            [7, 6, 6, '2025-06-20', '09:30:00', 'Nghẹt mũi, chảy nước mũi sau, đau hốc mắt', 'Đã điều trị kháng sinh 3 ngày', 'Viêm xoang hàm', 170000, 'momo', 'paid', 'MOMO_MNO345', 'confirmed', true],
            [8, 7, 7, '2025-06-21', '13:00:00', 'Nhìn mờ, nhìn đôi, đau đầu', 'Cận thị 3 độ, đeo kính', 'Tật khúc xạ', 190000, 'cash', 'unpaid', null, 'pending', false],
            [9, 8, 8, '2025-06-22', '16:00:00', 'Mất ngủ, lo lắng, hồi hộp', 'Stress công việc', 'Rối loạn lo âu', 250000, 'momo', 'paid', 'MOMO_PQR678', 'completed', true],
            [10, 9, 10, '2025-06-23', '10:00:00', 'Đau bụng vùng thượng vị, ợ hơi', 'Ăn nhiều dầu mỡ', 'Viêm dạ dày trào ngược', 180000, 'momo', 'paid', 'MOMO_STU901', 'confirmed', true],
            [11, 10, 1, '2025-06-24', '14:30:00', 'Hoa mắt, chóng mặt, ù tai', 'Huyết áp thấp 90/60', 'Thiểu năng tuần hoàn não', 230000, 'cash', 'unpaid', null, 'pending', false],
            [12, 1, 2, '2025-06-25', '08:00:00', 'Mụn trứng cá viêm đỏ', 'Đã dùng thuốc bôi 1 tháng', 'Trứng cá độ II', 140000, 'momo', 'paid', 'MOMO_VWX234', 'confirmed', true],
            [13, 2, 3, '2025-06-25', '09:00:00', 'Khò khè, khó thở về đêm', 'Tiền sử hen suyễn', 'Cơn hen phế quản', 200000, 'momo', 'paid', 'MOMO_YZA567', 'confirmed', true],
            [14, 3, 4, '2025-06-26', '10:00:00', 'Huyết áp tăng lên 165/100', 'Quên uống thuốc 3 ngày', 'Tăng huyết áp độ 2', 400000, 'momo', 'paid', 'MOMO_BCD890', 'completed', true],
            [15, 4, 5, '2025-06-26', '11:00:00', 'Đau nhức khớp gối, cứng khớp buổi sáng', 'Thoái hóa khớp gối độ 2', 170000, 'cash', 'unpaid', null, 'confirmed', false],
        ];
        $this->writeCsv('appointments.csv', $rows);
        $this->info('  ✅ appointments.csv — ' . (count($rows) - 1) . ' cuộc hẹn');
    }

    private function generatePaymentsCsv(): void
    {
        $rows = [
            ['id', 'appointment_id', 'patient_id', 'amount', 'method', 'transaction_id', 'order_id', 'request_id', 'raw_response', 'status'],
            [1, 1, 1, 200000, 'momo', 'MOMO_ABC123', 'ORDER_ABC123', 'REQ_ABC123', '{"resultCode":0,"message":"Success"}', 'success'],
            [2, 3, 2, 350000, 'momo', 'MOMO_DEF456', 'ORDER_DEF456', 'REQ_DEF456', '{"resultCode":0,"message":"Success"}', 'success'],
            [3, 4, 3, 180000, 'momo', 'MOMO_GHI789', 'ORDER_GHI789', 'REQ_GHI789', '{"resultCode":0,"message":"Success"}', 'success'],
            [4, 6, 5, 160000, 'momo', 'MOMO_JKL012', 'ORDER_JKL012', 'REQ_JKL012', '{"resultCode":0,"message":"Success"}', 'success'],
            [5, 7, 6, 170000, 'momo', 'MOMO_MNO345', 'ORDER_MNO345', 'REQ_MNO345', '{"resultCode":0,"message":"Success"}', 'success'],
            [6, 9, 8, 250000, 'momo', 'MOMO_PQR678', 'ORDER_PQR678', 'REQ_PQR678', '{"resultCode":0,"message":"Success"}', 'success'],
            [7, 10, 9, 180000, 'momo', 'MOMO_STU901', 'ORDER_STU901', 'REQ_STU901', '{"resultCode":0,"message":"Success"}', 'success'],
            [8, 12, 1, 140000, 'momo', 'MOMO_VWX234', 'ORDER_VWX234', 'REQ_VWX234', '{"resultCode":0,"message":"Success"}', 'success'],
            [9, 13, 2, 200000, 'momo', 'MOMO_YZA567', 'ORDER_YZA567', 'REQ_YZA567', '{"resultCode":0,"message":"Success"}', 'success'],
            [10, 14, 3, 400000, 'momo', 'MOMO_BCD890', 'ORDER_BCD890', 'REQ_BCD890', '{"resultCode":0,"message":"Success"}', 'success'],
        ];
        $this->writeCsv('payments.csv', $rows);
        $this->info('  ✅ payments.csv — ' . (count($rows) - 1) . ' giao dịch');
    }

    private function generateReviewsCsv(): void
    {
        $rows = [
            ['id', 'patient_id', 'doctor_id', 'appointment_id', 'rating', 'comment'],
            [1, 1, 1, 1, 5, 'Bác sĩ An rất tận tâm, khám kỹ lưỡng. Tôi đã đỡ ho sau 2 ngày.'],
            [2, 2, 4, 3, 4, 'Giải thích rõ ràng, nhưng chờ hơi lâu.'],
            [3, 3, 2, 4, 5, 'Bác sĩ Bình rất dễ thương, bé nhà tôi hết sợ hãi.'],
            [4, 4, 9, 5, 4, 'Tư vấn tốt, nhưng giá hơi cao.'],
            [5, 6, 6, 7, 5, 'Điều trị viêm xoang hiệu quả, khỏi hẳn sau 1 tuần.'],
            [6, 7, 7, 8, 3, 'Phòng khám thiếu máy móc, phải chuyển viện.'],
            [7, 9, 10, 10, 5, 'Bác sĩ Lan kê đơn thuốc hợp lý, đỡ đau bụng ngay.'],
            [8, 1, 2, 12, 5, 'Da tôi hết mụn sau 2 tuần, cảm ơn bác sĩ Nga.'],
            [9, 2, 3, 13, 4, 'Phòng khám sạch sẽ, bác sĩ nhiệt tình.'],
            [10, 3, 4, 14, 5, 'Giáo sư Oanh rất giỏi, huyết áp tôi đã ổn định.'],
        ];
        $this->writeCsv('reviews.csv', $rows);
        $this->info('  ✅ reviews.csv — ' . (count($rows) - 1) . ' đánh giá');
    }

    // ==================== IMPORT DỮ LIỆU ====================

    private function importSpecialties(): void
    {
        if (!Schema::hasTable('specialties')) {
            $this->warn('  ⚠️  Bảng specialties không tồn tại, bỏ qua.');
            return;
        }
        $rows = $this->readCsv('specialties.csv');
        $count = 0;
        $columns = Schema::getColumnListing('specialties');
        foreach ($rows as $row) {
            $data = [];
            foreach (['id', 'name', 'slug', 'icon', 'image', 'description', 'status'] as $col) {
                if (in_array($col, $columns) && isset($row[$col])) {
                    $data[$col] = $row[$col];
                }
            }
            if (!empty($data)) {
                DB::table('specialties')->updateOrInsert(['id' => $row['id']], $data);
                $count++;
            }
        }
        $this->info("  ✅ specialties: {$count} bản ghi đã lưu");
    }

    private function importDoctors(): void
    {
        if (!Schema::hasTable('doctors')) {
            $this->warn('  ⚠️  Bảng doctors không tồn tại, bỏ qua.');
            return;
        }
        $rows = $this->readCsv('doctors.csv');
        $count = 0;
        $columns = Schema::getColumnListing('doctors');
        foreach ($rows as $row) {
            $data = [];
            foreach (['id', 'specialty_id', 'name', 'email', 'phone', 'avatar', 'degree', 'clinic_name', 'clinic_address', 'clinic_district', 'clinic_city', 'latitude', 'longitude', 'experience_years', 'consultation_fee', 'rating', 'total_reviews', 'description', 'working_hours', 'status'] as $col) {
                if (in_array($col, $columns) && isset($row[$col]) && $row[$col] !== '') {
                    $data[$col] = $row[$col];
                }
            }
            if (!empty($data)) {
                DB::table('doctors')->updateOrInsert(['id' => $row['id']], $data);
                $count++;
            }
        }
        $this->info("  ✅ doctors: {$count} bản ghi đã lưu");
    }

    private function importPatients(): void
    {
        if (!Schema::hasTable('patients')) {
            $this->warn('  ⚠️  Bảng patients không tồn tại, bỏ qua.');
            return;
        }
        $rows = $this->readCsv('patients.csv');
        $count = 0;
        $columns = Schema::getColumnListing('patients');
        foreach ($rows as $row) {
            $data = [];
            foreach (['id', 'name', 'email', 'password', 'phone', 'avatar', 'birthday', 'gender', 'address', 'district', 'city', 'latitude', 'longitude', 'blood_type', 'medical_history', 'status'] as $col) {
                if (in_array($col, $columns) && isset($row[$col]) && $row[$col] !== '') {
                    $data[$col] = $row[$col];
                }
            }
            if (!empty($data)) {
                DB::table('patients')->updateOrInsert(['id' => $row['id']], $data);
                $count++;
            }
        }
        $this->info("  ✅ patients: {$count} bản ghi đã lưu");
    }

    private function importAppointments(): void
    {
        if (!Schema::hasTable('appointments')) {
            $this->warn('  ⚠️  Bảng appointments không tồn tại, bỏ qua.');
            return;
        }
        $rows = $this->readCsv('appointments.csv');
        $count = 0;
        $columns = Schema::getColumnListing('appointments');
        foreach ($rows as $row) {
            $data = [];
            foreach (['id', 'patient_id', 'doctor_id', 'appointment_date', 'appointment_time', 'symptoms', 'note', 'diagnosis', 'fee', 'payment_method', 'payment_status', 'payment_transaction_id', 'status', 'reminder_sent'] as $col) {
                if (in_array($col, $columns) && isset($row[$col]) && $row[$col] !== '') {
                    $data[$col] = $row[$col];
                }
            }
            if (!empty($data)) {
                DB::table('appointments')->updateOrInsert(['id' => $row['id']], $data);
                $count++;
            }
        }
        $this->info("  ✅ appointments: {$count} bản ghi đã lưu");
    }

    private function importPayments(): void
    {
        if (!Schema::hasTable('payments')) {
            $this->warn('  ⚠️  Bảng payments không tồn tại, bỏ qua.');
            return;
        }
        $rows = $this->readCsv('payments.csv');
        $count = 0;
        $columns = Schema::getColumnListing('payments');
        foreach ($rows as $row) {
            $data = [];
            foreach (['id', 'appointment_id', 'patient_id', 'amount', 'method', 'transaction_id', 'order_id', 'request_id', 'raw_response', 'status'] as $col) {
                if (in_array($col, $columns) && isset($row[$col]) && $row[$col] !== '') {
                    $data[$col] = $row[$col];
                }
            }
            if (!empty($data)) {
                DB::table('payments')->updateOrInsert(['id' => $row['id']], $data);
                $count++;
            }
        }
        $this->info("  ✅ payments: {$count} bản ghi đã lưu");
    }

    private function importReviews(): void
    {
        if (!Schema::hasTable('reviews')) {
            $this->warn('  ⚠️  Bảng reviews không tồn tại, bỏ qua.');
            return;
        }
        $rows = $this->readCsv('reviews.csv');
        $count = 0;
        $columns = Schema::getColumnListing('reviews');
        foreach ($rows as $row) {
            $data = [];
            foreach (['id', 'patient_id', 'doctor_id', 'appointment_id', 'rating', 'comment'] as $col) {
                if (in_array($col, $columns) && isset($row[$col]) && $row[$col] !== '') {
                    $data[$col] = $row[$col];
                }
            }
            if (!empty($data)) {
                DB::table('reviews')->updateOrInsert(['id' => $row['id']], $data);
                $count++;
            }
        }
        $this->info("  ✅ reviews: {$count} bản ghi đã lưu");
    }

    // ==================== HELPER CSV ====================

    private function writeCsv(string $filename, array $rows): void
    {
        $path = $this->csvDir . '/' . $filename;
        $fp = fopen($path, 'w');
        fwrite($fp, "\xEF\xBB\xBF");
        foreach ($rows as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
    }

    private function readCsv(string $filename): array
    {
        $path = $this->csvDir . '/' . $filename;
        if (!file_exists($path)) {
            $this->error("  ❌ Không tìm thấy file: {$path}");
            return [];
        }
        $rows = [];
        $headers = null;
        $fp = fopen($path, 'r');
        $bom = fread($fp, 3);
        if ($bom !== "\xEF\xBB\xBF") rewind($fp);
        while (($row = fgetcsv($fp)) !== false) {
            if ($headers === null) {
                $headers = $row;
                continue;
            }
            if (count($row) === count($headers)) {
                $rows[] = array_combine($headers, $row);
            }
        }
        fclose($fp);
        return $rows;
    }
}