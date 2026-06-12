<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DashboardModel extends Model
{
    use HasFactory;

    public function getSummary()
    {
        $doctorWorking = DB::table('tbl_doctors')
            ->where('availability', 1)
            ->count();
        $countBooking = DB::table('tbl_appointments')
            ->where('appointmentStatus', '!=', 'c')
            ->count();
        $totalAmount = DB::table('tbl_checkout')
            ->where('paymentStatus', 'y')
            ->sum('amount');

        // Trả về mảng chứa các dữ liệu tổng hợp
        return [
            'doctorWorking' => $doctorWorking,
            'countBooking' => $countBooking,
            'totalAmount' => $totalAmount,
        ];
    }

    public function getValueDomain()
    {
        // Lấy số lượng doctors cho mỗi miền (b, t, n)
        return DB::table('tbl_doctors')
            ->select(DB::raw('domain, COUNT(*) as count'))
            ->whereIn('domain', ['b', 't', 'n'])  // Chỉ lấy các miền có domain b, t, n
            ->groupBy('domain')  // Nhóm theo domain
            ->get()
            ->pluck('count', 'domain');  // Trả về mảng với key là domain và value là count
    }

    public function getValuePayment()
    {
        return DB::table('tbl_checkout')
            ->select('paymentMethod', \DB::raw('COUNT(*) as count'))
            ->groupBy('paymentMethod')
            ->get()
            ->toArray();
    }

    public function getMostdoctorBooked()
    {
        return DB::table('tbl_doctors')
            ->join('tbl_appointments', 'tbl_doctors.doctorId', '=', 'tbl_appointments.doctorId')
            ->select('tbl_doctors.doctorId', 'tbl_doctors.title', 'tbl_doctors.quantity', DB::raw('SUM(tbl_appointments.numAdults + tbl_appointments.numChildren) as booked_quantity'))
            ->groupBy('tbl_doctors.doctorId', 'tbl_doctors.quantity', 'tbl_doctors.title')
            ->orderByDesc(DB::raw('SUM(tbl_appointments.numAdults + tbl_appointments.numChildren)')) // Sắp xếp theo số lượng đặt doctor giảm dần
            ->take(3) // Lấy 3 doctor có số lượng đặt cao nhất
            ->get();
    }

    public function getNewBooking()
    {
        return DB::table('tbl_appointments')
            ->join('tbl_doctors', 'tbl_appointments.doctorId', '=', 'tbl_doctors.doctorId')
            ->where('tbl_appointments.appointmentStatus', 'b')
            ->orderByDesc('tbl_appointments.appointmentDate')
            ->select('tbl_appointments.*', 'tbl_doctors.title as doctor_name') // Chọn tất cả các cột từ tbl_appointments và thêm tên doctor từ tbl_doctors
            ->take(3)
            ->get();

    }

    public function getRevenuePerMonth()
    {
        $monthlyRevenue = DB::table('tbl_appointments')
            ->select(DB::raw('MONTH(appointmentDate) as month, SUM(totalPrice) as revenue'))
            ->where('appointmentStatus', 'y')
            ->groupBy(DB::raw('MONTH(appointmentDate)'))
            ->orderBy('month', 'asc')
            ->get();

        // Chuẩn bị mảng doanh thu với 12 tháng
        $revenueData = array_fill(0, 12, 0);  // Mảng chứa doanh thu cho 12 tháng

        // Gán doanh thu cho từng tháng
        foreach ($monthlyRevenue as $data) {
                $revenueData[$data->month - 1] = $data->revenue;  // Gán doanh thu cho tháng tương ứng
        }

        return $revenueData;
    }



}
