<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\ApiSolicitudController;
use Illuminate\Console\Command;

class ApiSolicitudes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:apiSolicitudes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta las Solicitudes a API pendientes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // No utilizado actualmente, reemplazado por Jobs Laravel

        $apiController = new ApiSolicitudController();

        $solicitudes = $apiController->obtenerSolicitudes(10);

        foreach ($solicitudes as $solicitud) {

            echo "Resolviendo solicitud : " . $solicitud->id;
            $retorno = $apiController->resolverSolicitud($solicitud->id);

            print_r($retorno);
        }
        return Command::SUCCESS;
    }
}
