<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view('clients.contact')->with('title', 'Liên hệ');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email',
            'phone'   => 'nullable|string|max:15',
            'message' => 'required|string|max:1000',
        ]);

        try {
            Mail::raw(
                "Liên hệ mới từ: {$request->name}\n"
                . "Email: {$request->email}\n"
                . "SĐT: {$request->phone}\n"
                . "Nội dung: {$request->message}",
                function ($msg) {
                    $msg->to(env('MAIL_FROM_ADDRESS'))
                        ->subject('[DoctorCam] Liên hệ mới');
                }
            );
        } catch (\Exception $e) {
            \Log::error('Contact mail error: ' . $e->getMessage());
        }

        return back()->with('success', 'Gửi thành công! Chúng tôi sẽ sớm liên hệ lại.');
    }
}