<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\GenerateCsvAndImport::class,
        \App\Console\Commands\SendAppointmentReminders::class, // ✅ thêm vào đây
    ];

    // ✅ Chỉ giữ 1 hàm schedule() duy nhất
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('appointments:send-reminders')->dailyAt('08:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}