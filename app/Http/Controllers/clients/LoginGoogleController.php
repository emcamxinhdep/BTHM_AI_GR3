<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\clients\Patient;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class LoginGoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $patient = Patient::where('email', $googleUser->email)->first();

            if (!$patient) {
                $patient = Patient::create([
                    'name'      => $googleUser->name,
                    'email'     => $googleUser->email,
                    'password'  => Hash::make(Str::random(16)),
                    'avatar'    => $googleUser->avatar,
                    'status'    => 1,
                ]);
            }

            session([
                'patient_id'    => $patient->id,
                'patient_name'  => $patient->name,
                'patient_email' => $patient->email,
                'patient_avatar'=> $patient->avatar,
            ]);

            return redirect()->route('home');

        } catch (\Exception $e) {
            \Log::error('Google login error: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Đăng nhập Google thất bại. Vui lòng thử lại.');
        }
    }
}