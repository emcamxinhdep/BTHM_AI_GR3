<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('admin_id')) {
            toastr()->error('Vui lòng đăng nhập vào Admin để thực hiện.', 'Thông báo');
            return redirect()->route('admin.login');
        }

        return $next($request);
    }

    public function boot(): void
    {
        View::composer('admin.layouts.admin', function ($view) {
            if (session('admin_id')) {
                $view->with('currentAdmin', (new AdminModel())->getAdmin());
            }
        });
    }
}