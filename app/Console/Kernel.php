<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

use App\Jobs\PosiContJob;

class Kernel extends ConsoleKernel
{

    
    protected $commands = [
        'App\Jobs\PosiContJob'
    ];
    

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        Log::channel()->info('[*]['.date('H:i:s')."] Inicializacion de CRONS.... .\r\n");


        // Validamos los post programados para su activación
        $schedule->job(new PosiContJob)->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
