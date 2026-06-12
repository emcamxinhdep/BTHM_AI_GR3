<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\clients\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function index()
    {
        return view('clients.login')->with('title', 'Đăng nhập');
    }

    public function login(Request $request)
    {
        $username = $request->input('username_login') ?: $request->input('username');
        $password = $request->input('password_login') ?: $request->input('password');

        if (empty($username) || empty($password)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng nhập tên đăng nhập và mật khẩu.'], 422);
        }

        $patient = Patient::where('email', $username)
                    ->orWhere('name', $username)
                    ->first();

        if (!$patient || !Hash::check($password, $patient->password)) {
            return response()->json(['success' => false, 'message' => 'Sai tên đăng nhập hoặc mật khẩu.'], 401);
        }

        session([
            'patient_id'    => $patient->id,
            'patient_name'  => $patient->name,
            'patient_email' => $patient->email,
            'patient_avatar'=> $patient->avatar,
        ]);

        return response()->json([
            'success' => true,
            'redirect' => route('home')
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'username_register' => 'required|string|max:100',
            'email_register'    => 'required|email|unique:patients,email',
            'password_register' => 'required|min:6',
            're_pass'           => 'required|same:password_register',
        ], [
            'username_register.required' => 'Vui lòng nhập họ tên',
            'email_register.required'    => 'Vui lòng nhập email',
            'email_register.email'       => 'Email không đúng định dạng',
            'email_register.unique'      => 'Email đã được đăng ký',
            'password_register.required' => 'Vui lòng nhập mật khẩu',
            'password_register.min'      => 'Mật khẩu phải có ít nhất 6 ký tự',
            're_pass.required'           => 'Vui lòng nhập lại mật khẩu',
            're_pass.same'               => 'Mật khẩu nhập lại không khớp',
        ]);

        $patient = Patient::create([
            'name'     => $request->username_register,
            'email'    => $request->email_register,
            'password' => Hash::make($request->password_register),
            'phone'    => $request->phone ?? null,
            'status'   => 1,
        ]);

        if ($patient) {
            return redirect()->route('login')->with('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
        } else {
            return back()->with('error', 'Đăng ký thất bại, vui lòng thử lại.');
        }
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login')->with('success', 'Đã đăng xuất.');
    }
}