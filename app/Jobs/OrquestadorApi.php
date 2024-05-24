<?php

namespace App\Jobs;

use App\Http\Controllers\Api\ApiSolicitudController;
use App\Http\Controllers\Logger;
use App\Models\Api\ApiSolicitudes;
use App\Models\FLU\FLU_Notificaciones;
use App\Models\PV\PV_PostVenta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrquestadorApi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public ApiSolicitudes $solicitud)
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $logger = new Logger();
        $logger->info("Ejecutando job para solicitud : " . $this->solicitud->id);

        $ordenID = $this->solicitud->ReferenciaID;
        $flujoID = $this->solicitud->FlujoID;

        $logger->info("Datos solicitud = referencia ID : " . $ordenID . " - Flujo ID : ".$flujoID);

        $controller = new ApiSolicitudController();
        $resp = $controller->resolverSolicitud($this->solicitud);

        $logger->info(print_r($resp, true));

        if ($resp["status"] == "OK") {

            try {
                FLU_Notificaciones::Notificar($ordenID, $flujoID);
                $logger->info("Orden : " . $ordenID . " Notificada ");

            } catch (\Exception $exception) {
                $logger->error("Error en Orden : " . $ordenID . " : " . $exception->getMessage());
            }

        } else {
            $logger->error($resp["message"] ?? 'No se pudo resolver la solicitud');
        }

        $logger->solveArray($this->solicitud->id);

    }
}
