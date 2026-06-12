<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admin\AdminModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminManagementController extends Controller
{
    private const AVATAR_DIR = 'admin/assets/images/user-profile';
    
    private $admin;

    public function __construct()
    {
        $this->admin = new AdminModel();
    }
    public function index()
    {
        $title = 'Quản lý Admin';

        $admin = $this->admin->getAdmin();

        return view('admin.profile-admin', compact('title', 'admin'));
    }

    public function updateAdmin(Request $request)
    {
        $name = $request->name;
        $password = $request->password;
        $email = $request->email;
        $phone = $request->phone;

        $admin = $this->admin->getAdmin();
        $oldPass = $admin->password;

        if ($password != $oldPass) {
            $password = md5($password);
        }

        $dataUpdate = [
            'name' => $name,
            'password' => $password,
            'email' => $email,
            'phone' => $phone,
        ];
        $update = $this->admin->updateAdmin($dataUpdate);
        $newinfo = $this->admin->getAdmin();
        if ($update) {
            return response()->json(['success' => true, 'data' => $newinfo]);
        } else {
            return response()->json(['success' => false, 'message' => 'Không có thông tin nào thay đổi!']);
        }
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatarAdmin' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $admin = $this->admin->getAdmin();
        $destinationPath = public_path(self::AVATAR_DIR);

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // Xóa ảnh cũ nếu có
        if ($admin->avatar) {
            $oldFile = $destinationPath . '/' . $admin->avatar;
            if (file_exists($oldFile)) {
                @unlink($oldFile);
            }
        }

        $file = $request->file('avatarAdmin');
        $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
        $file->move($destinationPath, $filename);

        $this->admin->updateAdmin(['avatar' => $filename]);

        return response()->json([
            'success'    => true,
            'message'    => 'Cập nhật ảnh thành công!',
            'avatar_url' => asset(self::AVATAR_DIR . '/' . $filename),
        ]);
    }

}
