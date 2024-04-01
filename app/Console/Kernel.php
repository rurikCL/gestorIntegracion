<?php

namespace App\Console;

use App\Http\Controllers\Flujo\FlujoController;
use App\Http\Controllers\Flujo\FlujoHubspotController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        $schedule->call(function () {
            $flujoControl = new FlujoController();
            // Envio de Leads MG
            $res = $flujoControl->sendLeadMG();
            // Envio de Cotizaciones MG
            $res = $flujoControl->sendCotizacionMG();

            // Envio de Ventas Indumotora
            $res = $flujoControl->sendVentasIndumotora();

            $res = $flujoControl->sendOtIndumotora();

        })->name("Control de Flujos : 30 minutos")->everyThirtyMinutes();


        $schedule->call(function () {
            $flujoControl = new FlujoController();
        })->name("Control de Flujos : 4 Horas")->everyFourHours();


        $schedule->call(function () {
            $flujoControl = new FlujoController();
//            $flujoControl->leadsHubspot(); // flujo hubspot contactos

            $flujoNegocio = new FlujoHubspotController();
            $flujoNegocio->leadsHubspotDeals(); // flujo hubspot negocios

            $flujoControl->reprocesarSolicitudes();

        })->name("Control de Flujos : 5 minutos")->everyFiveMinutes();


        $schedule->call(function () {
            $flujoControl = new FlujoController();

            $flujoControl->actualizaStockAPC();
            $flujoControl->autoredTransactions();

        })->name("Control de Flujos : 2 veces al dia")->twiceDaily(7, 14);


        $schedule->call(function () {
            $flujoControl = new FlujoController();
            $res = $flujoControl->sendOTsSICIndumotora();

        })->name("Control de Flujos : 1 vez al dia (madrugada)")->dailyAt('02:00');



//        foreach (['08:45', '09:15', '09:45', '10:15'] as $time) {
//            $schedule->command('command:name')->dailyAt($time);
//        }

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
