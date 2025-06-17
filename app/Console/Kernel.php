<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Registra los comandos Artisan personalizados.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\ImportXpdl::class,
    ];

    /**
     * Define el schedule de comandos.
     */
    protected function schedule(Schedule $schedule)
    {
        // ...
    }

    /**
     * Registra los comandos de consola de la aplicación.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
