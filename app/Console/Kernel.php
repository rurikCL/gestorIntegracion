<?php

namespace App\Console;

use App\Http\Controllers\Flujo\FlujoController;
use App\Http\Controllers\Flujo\FlujoHubspotController;
use App\Http\Controllers\RobotApcController;
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


        // FLUJO CADA 30 Minutos  -----------------------
        $schedule->call(function () {
            $flujoControl = new FlujoController();
            // Envio de Leads MG
            $res = $flujoControl->sendLeadMG();
            // Envio de Cotizaciones MG
            $res = $flujoControl->sendCotizacionMG();


            // Envio de Ventas Indumotora
            $res = $flujoControl->sendVentasIndumotora();
            // Envio de OTs Indumotora
            $res = $flujoControl->sendOtIndumotora();

        })->name("Control de Flujos : 30 minutos")->everyThirtyMinutes();


        // FLUJO CADA 4 Horas
        $schedule->call(function () {
//            $flujoControl = new FlujoController();
        })->name("Control de Flujos : 4 Horas")->everyFourHours();


        // FLUJO CADA 5 minutos -------------
        $schedule->call(function () {
            $flujoControl = new FlujoController();

            $flujoNegocio = new FlujoHubspotController();
            $flujoNegocio->leadsHubspotDeals(); // flujo hubspot negocios

            $flujoNegocio->actualizaLeadHubspot(); // Actualiza estado Pipeline de Deal en Hubspot

            // Proceso de reproceso de solicitudes pendientes (que tengan intentos pendientes)
            $flujoControl->reprocesarSolicitudes();

        })->name("Control de Flujos : 5 minutos")->everyFiveMinutes();


        // FLUJO 2 Veces al dia (7am, 14pm)
        $schedule->call(function () {
            $flujoControl = new FlujoController();

            $flujoControl->actualizaStockAPC();

            // Extraccion datos Autored --------
            $flujoControl->autoredTransactions();
            $flujoControl->autoredInspections();

        })->name("Control de Flujos : 2 veces al dia")->twiceDaily(7, 14);


        // ROBOT APC STOCK --------
        $schedule->call(function () {
            $robotControl = new RobotApcController();
            $robotControl->traeStockAnual();
        })->name("Control de Robot APC Stock : 2 veces al dia")->twiceDaily(3, 13);


        // FLUJO DIARIO 2am --------------
        $schedule->call(function () {
            $flujoControl = new FlujoController();

            $res = $flujoControl->sendOTsSICIndumotora();
            $flujoControl->cargaIndicadoresUF();

        })->name("Control de Flujos : 1 vez al dia (madrugada)")->dailyAt('02:00');


        // FLUJO MENSUAL (Primer Dia) ------
        $schedule->call(function () {
            $flujoControl = new FlujoController();
            $flujoControl->cargaIndicadoresUTM();
        })->name("Control de Flujos : 1 vez al mes (primer dia)")->monthlyOn(1);


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
