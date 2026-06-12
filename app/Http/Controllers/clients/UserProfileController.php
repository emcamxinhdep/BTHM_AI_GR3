<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\clients\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserProfileController extends Controller
{
    /**
     * Thư mục lưu avatar, tính từ public/
     */
    private const AVATAR_DIR = 'clients/assets/images/upload';

    public function index()
    {
        $user = Patient::find(session('patient_id'));

        if (!$user) {
            return redirect()->route('login');
        }

        return view('clients.user-profile', compact('user'))
            ->with('title', 'Thông tin cá nhân');
    }

    public function update(Request $request)
    {
        // Nếu là AJAX request từ jQuery nhưng không có name → bỏ qua
        if (($request->ajax() || $request->wantsJson()) && !$request->filled('name')) {
            return response()->json(['success' => false, 'message' => 'Ignored'], 200);
        }

        $patient = Patient::findOrFail(session('patient_id'));

        $request->validate([
            'name'            => 'required|string|max:100',
            'phone'           => 'nullable|string|max:15',
            'birthday'        => 'nullable|date',
            'gender'          => 'nullable|in:male,female,other',
            'address'         => 'nullable|string|max:255',
            'district'        => 'nullable|string|max:100',
            'city'            => 'nullable|string|max:100',
            'blood_type'      => 'nullable|in:A,B,O,AB',
            'medical_history' => 'nullable|string|max:2000',
        ]);

        $patient->update($request->only([
            'name', 'phone', 'birthday', 'gender',
            'address', 'district', 'city', 'blood_type', 'medical_history'
        ]));

        session(['patient_name' => $request->name]);

        return redirect()->route('user-profile')->with('success', 'Cập nhật thông tin thành công.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:6|confirmed',
        ]);

        $patient = Patient::findOrFail(session('patient_id'));

        if (!Hash::check($request->current_password, $patient->password)) {
            return back()->with('error', 'Mật khẩu hiện tại không đúng.');
        }

        $patient->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Đổi mật khẩu thành công.');
    }

    /**
     * Đổi ảnh đại diện - lưu trực tiếp vào public/clients/assets/images/upload
     * Trong DB chỉ lưu tên file (vd: "abc123.jpg"), không lưu cả đường dẫn.
     */
    public function changeAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png|max:5120', // 5MB
        ]);

        $patient = Patient::findOrFail(session('patient_id'));

        $destinationPath = public_path(self::AVATAR_DIR);

        // Tạo thư mục nếu chưa tồn tại
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // Xóa ảnh cũ nếu có
        if ($patient->avatar) {
            $oldFile = $destinationPath . '/' . $patient->avatar;
            if (file_exists($oldFile)) {
                @unlink($oldFile);
            }
        }

        // Tạo tên file mới duy nhất
        $file = $request->file('avatar');
        $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();

        // Di chuyển file vào public/clients/assets/images/upload
        $file->move($destinationPath, $filename);

        // Chỉ lưu tên file trong DB
        $patient->update(['avatar' => $filename]);
        session(['patient_avatar' => $filename]);

        $avatarUrl = asset(self::AVATAR_DIR . '/' . $filename);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Cập nhật ảnh đại diện thành công.',
                'avatar_url' => $avatarUrl,
            ]);
        }

        return back()->with('success', 'Cập nhật ảnh đại diện thành công.');
    }
}