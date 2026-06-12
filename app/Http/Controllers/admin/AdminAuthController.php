<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    private const ADMIN_USERNAME = 'admin';
    private const ADMIN_PASSWORD = 'admin123456';

    public function showLogin()
    {
        if (session('admin_id')) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);

        if (
            $request->username === self::ADMIN_USERNAME &&
            $request->password === self::ADMIN_PASSWORD
        ) {
            session(['admin_id' => 1, 'admin_email' => $request->username]);
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['username' => 'Tên đăng nhập hoặc mật khẩu không đúng.'])->withInput();
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['admin_id', 'admin_email']);
        return redirect()->route('admin.login');
    }
}