<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckLoggedInClients
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('patient_id')) {
            // Xóa dòng toastr bên dưới
            // toastr()->error('Vui lòng đăng nhập để thực hiện.', 'Thông báo');
            return redirect()->route('login');
        }

        return $next($request);
    }
}