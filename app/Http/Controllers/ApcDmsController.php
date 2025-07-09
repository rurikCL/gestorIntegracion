<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\ApiSolicitudController;
use App\Models\Api\ApiSolicitudes;
use App\Models\FLU\FLU_Flujos;
use App\Models\FLU\FLU_Homologacion;
use App\Models\MA\MA_Bancos;
use App\Models\MA\MA_Marcas;
use App\Models\MA\MA_Modelos;
use App\Models\MA\MA_Sucursales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ApcDmsController extends Controller
{
    //

    public function getSucursales()
    {
        echo "Ejecutando Flujo APC DMS Sucursales <br>";
        Log::info("Inicio de Flujo APC DMS Sucursales");

        $flujo = FLU_Flujos::where('Nombre', 'APC_NV')->first();

        if ($flujo->Activo) {
            $h = new FLU_Homologacion();

            $solicitudCon = new ApiSolicitudController();

            $referencia = $flujo->ID . date("ymdh");

            $req = new Request();
            $req['referencia_id'] = $referencia;
            $req['api_id'] = 16;
            $req['proveedor_id'] = 11;
            $req['prioridad'] = 1;
            $req['flujoID'] = $flujo->ID;
            $req['onDemand'] = true;

            $resp = $solicitudCon->store($req);
            $resp = $resp->getData();

            $solicitud = ApiSolicitudes::where('id', $resp->id)->first();

            if (substr($solicitud->Respuesta, 0, 4) == 'file') {
                $nombre = substr($solicitud->Respuesta, 5, strlen($solicitud->Respuesta));
                Log::info("Archivo json generado " . $nombre);

                $arrayData = json_decode(Storage::get($nombre));
            } else {
                $arrayData = json_decode($solicitud->Respuesta);
            }

            foreach ($arrayData->listSucursales as $dato) {

                $idSucursalAPC = $dato->idSucursalDTO;
                $nombreSucursalAPC = $dato->nombreSucursalDTO;

                $sucursal = MA_Sucursales::where('Sucursal', 'like', $nombreSucursalAPC)->first();

                $h->Nuevo([
                    'flujo' => $flujo->ID,
                    'identificador' => $sucursal->ID ?? '', // ID SUCURSAL POMPEYO
                    'respuesta' => $idSucursalAPC,
                    'nombre' => $nombreSucursalAPC,
                    'codigo' => 'sucursal'
                ]);
            }

        }
    }

    public function getMarcas()
    {
        echo "Ejecutando Flujo APC DMS Marcas <br>";
        Log::info("Inicio de Flujo APC DMS Marcas");

        $flujo = FLU_Flujos::where('Nombre', 'APC_NV')->first();

        if ($flujo->Activo) {
            $h = new FLU_Homologacion();

            $solicitudCon = new ApiSolicitudController();

            $referencia = $flujo->ID . date("ymdh");

            $req = new Request();
            $req['referencia_id'] = $referencia;
            $req['api_id'] = 17;
            $req['proveedor_id'] = 11;
            $req['prioridad'] = 1;
            $req['flujoID'] = $flujo->ID;
            $req['onDemand'] = true;

            $resp = $solicitudCon->store($req);
            $resp = $resp->getData();

            $solicitud = ApiSolicitudes::where('id', $resp->id)->first();

            if (substr($solicitud->Respuesta, 0, 4) == 'file') {
                $nombre = substr($solicitud->Respuesta, 5, strlen($solicitud->Respuesta));
                Log::info("Archivo json generado " . $nombre);

                $arrayData = json_decode(Storage::get($nombre));
            } else {
                $arrayData = json_decode($solicitud->Respuesta);
            }

            foreach ($arrayData->listMarcas as $dato) {

                $idMarcaAPC = $dato->id_marcaDTO;
                $nombreMarcaAPC = $dato->nombreDTO;

                $marca = MA_Marcas::where('Marca', 'like', $nombreMarcaAPC)->first();

                $h->Nuevo([
                    'flujo' => $flujo->ID,
                    'identificador' => $marca->ID ?? '',
                    'respuesta' => $idMarcaAPC,
                    'nombre' => $nombreMarcaAPC,
                    'codigo' => 'marca'
                ]);

                $req = new Request();
                $req['marca_id'] = $idMarcaAPC;

                $this->getModelos($req);

            }

        }
    }
    public function getModelos(Request $request)
    {
        echo "Ejecutando Flujo APC DMS Modelos <br>";
        Log::info("Inicio de Flujo APC DMS Modelos");

        $flujo = FLU_Flujos::where('Nombre', 'APC_NV')->first();

        if ($flujo->Activo) {
            $h = new FLU_Homologacion();

            $solicitudCon = new ApiSolicitudController();

            $referencia = $flujo->ID . date("ymdh");

            $req = new Request();
            $req['referencia_id'] = $referencia;
            $req['api_id'] = 18;
            $req['proveedor_id'] = 11;
            $req['prioridad'] = 1;
            $req['flujoID'] = $flujo->ID;
            $req['onDemand'] = true;

            if($request->input('marca_id')){
                $req['dataHeader'] = "IdMarcaVehiculoDTO:" . $request->input('marca_id');
            }

            $resp = $solicitudCon->store($req);
            $resp = $resp->getData();

            $solicitud = ApiSolicitudes::where('id', $resp->id)->first();

            if (substr($solicitud->Respuesta, 0, 4) == 'file') {
                $nombre = substr($solicitud->Respuesta, 5, strlen($solicitud->Respuesta));
                Log::info("Archivo json generado " . $nombre);

                $arrayData = json_decode(Storage::get($nombre));
            } else {
                $arrayData = json_decode($solicitud->Respuesta);
            }

            if($arrayData){
                foreach ($arrayData->lisModelos as $dato) {

                    $idModeloAPC = $dato->id_modeloDTO;
                    $nombreModeloAPC = $dato->nombreDTO;

                    $modelo = MA_Modelos::where('Modelo', 'like', $nombreModeloAPC)->first();

                    $h->Nuevo([
                        'flujo' => $flujo->ID,
                        'identificador' => $modelo->ID ?? '',
                        'respuesta' => $idModeloAPC,
                        'nombre' => $nombreModeloAPC,
                        'codigo' => 'modelo'
                    ]);
                }
            }

        }
    }
    public function getColorExterno()
    {
        echo "Ejecutando Flujo APC DMS Color Externo <br>";
        Log::info("Inicio de Flujo APC DMS Color externo");

        $flujo = FLU_Flujos::where('Nombre', 'APC_NV')->first();

        if ($flujo->Activo) {
            $h = new FLU_Homologacion();

            $solicitudCon = new ApiSolicitudController();

            $referencia = $flujo->ID . date("ymdh");

            $req = new Request();
            $req['referencia_id'] = $referencia;
            $req['api_id'] = 19;
            $req['proveedor_id'] = 11;
            $req['prioridad'] = 1;
            $req['flujoID'] = $flujo->ID;
            $req['onDemand'] = true;

            $resp = $solicitudCon->store($req);
            $resp = $resp->getData();

            $solicitud = ApiSolicitudes::where('id', $resp->id)->first();

            if (substr($solicitud->Respuesta, 0, 4) == 'file') {
                $nombre = substr($solicitud->Respuesta, 5, strlen($solicitud->Respuesta));
                Log::info("Archivo json generado " . $nombre);

                $arrayData = json_decode(Storage::get($nombre));
            } else {
                $arrayData = json_decode($solicitud->Respuesta);
            }

            foreach ($arrayData->lisColorExterior as $dato) {

                $id = $dato->id_colorDTO;
                $nombre = $dato->nombreDTO;

                $h->Nuevo([
                    'flujo' => $flujo->ID,
                    'identificador' => $nombre,
                    'respuesta' => $id,
                    'nombre' => $nombre,
                    'codigo' => 'colorExterno'
                ]);
            }

        }
    }
    public function getColorInterno()
    {
        echo "Ejecutando Flujo APC DMS Color Interno <br>";
        Log::info("Inicio de Flujo APC DMS Color interno");

        $flujo = FLU_Flujos::where('Nombre', 'APC_NV')->first();

        if ($flujo->Activo) {
            $h = new FLU_Homologacion();

            $solicitudCon = new ApiSolicitudController();

            $referencia = $flujo->ID . date("ymdh");

            $req = new Request();
            $req['referencia_id'] = $referencia;
            $req['api_id'] = 20;
            $req['proveedor_id'] = 11;
            $req['prioridad'] = 1;
            $req['flujoID'] = $flujo->ID;
            $req['onDemand'] = true;

            $resp = $solicitudCon->store($req);
            $resp = $resp->getData();

            $solicitud = ApiSolicitudes::where('id', $resp->id)->first();

            if (substr($solicitud->Respuesta, 0, 4) == 'file') {
                $nombre = substr($solicitud->Respuesta, 5, strlen($solicitud->Respuesta));
                Log::info("Archivo json generado " . $nombre);

                $arrayData = json_decode(Storage::get($nombre));
            } else {
                $arrayData = json_decode($solicitud->Respuesta);
            }

            foreach ($arrayData->lisColorInterior as $dato) {

                $id = $dato->id_colorDTO;
                $nombre = $dato->nombreDTO;

                $h->Nuevo([
                    'flujo' => $flujo->ID,
                    'identificador' => $nombre,
                    'respuesta' => $id,
                    'nombre' => $nombre,
                    'codigo' => 'colorInterno'
                ]);
            }

        }
    }

    public function getBancos(){
        echo "Ejecutando Flujo APC DMS Bancos <br>";
        Log::info("Inicio de Flujo APC DMS Bancos");

        $flujo = FLU_Flujos::where('Nombre', 'APC_NV')->first();

        if ($flujo->Activo) {
            $h = new FLU_Homologacion();

            $solicitudCon = new ApiSolicitudController();

            $referencia = $flujo->ID . date("ymdh");

            $req = new Request();
            $req['referencia_id'] = $referencia;
            $req['api_id'] = 23;
            $req['proveedor_id'] = 11;
            $req['prioridad'] = 1;
            $req['flujoID'] = $flujo->ID;
            $req['onDemand'] = true;

            $resp = $solicitudCon->store($req);
            $resp = $resp->getData();

            $solicitud = ApiSolicitudes::where('id', $resp->id)->first();

            if (substr($solicitud->Respuesta, 0, 4) == 'file') {
                $nombre = substr($solicitud->Respuesta, 5, strlen($solicitud->Respuesta));
                Log::info("Archivo json generado " . $nombre);

                $arrayData = json_decode(Storage::get($nombre));
            } else {
                $arrayData = json_decode($solicitud->Respuesta);
            }

            foreach ($arrayData->listBancos as $dato) {

                $id = $dato->id_bancoDTO;
                $nombre = $dato->nombreDTO;
                $nombreSplit = explode(" (", $nombre);

                $banco = MA_Bancos::where('Banco', 'like', $nombreSplit[0])->first();

                $h->Nuevo([
                    'flujo' => $flujo->ID,
                    'identificador' => $banco->Banco ?? $nombre,
                    'respuesta' => $id,
                    'nombre' => $nombre,
                    'codigo' => 'banco'
                ]);
            }

        }
    }

    public function homologacionAPC()
    {
        echo "Ejecutando Flujo APC DMS <br>";
        $this->getSucursales();
        $this->getMarcas();
//        $this->getModelos();
        $this->getColorExterno();
        $this->getColorInterno();
        $this->getBancos();
        echo "Fin de Flujo APC DMS <br>";

    }
}
