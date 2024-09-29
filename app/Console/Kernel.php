<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\AuthPermissionCommand;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        AuthPermissionCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
        // Function calls to be run by the Laravel scheduler
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $home_controller = new \App\Http\Controllers\HomeController();
            $home_controller->risk_levels();
            $home_controller->opco_findings();
            $home_controller->open_close();
            $home_controller->num_of_hosts_by_cat();
            $home_controller->num_of_hosts_by_vuln_name();

            $api_request_controller = new \App\Http\Controllers\APIRequestController();
            $api_request_controller->requestAPI();
        })->everyMinute();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
