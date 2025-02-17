<?php

namespace App\Imports;

use AnourValar\EloquentSerialize\Tests\Models\File;
use App\Models\FLU\FLU_Homologacion;
use App\Models\MA\MA_Clientes;
use App\Models\MA\MA_Marcas;
use App\Models\MA\MA_Usuarios;
use App\Models\SIS\SIS_UsuariosSucursales;
use App\Models\VT\VT_CotizacionesSolicitudesCredito;
use App\Models\VT\VT_Cotizaciones;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FinancierasImport implements ToCollection, WithBatchInserts, WithHeadingRow
{
    private $carga = null;
    private $contadorRegistro = 0;
    private $contErrores = 0;
    private $fecha_inicio = 0;
    private $fecha_fin = 0;

    use Importable;

    public function __construct($carga, $fecha_inicio = '2023-11-01', $fecha_fin = '2023-11-30')
    {
        $this->carga = $carga;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
    }

    public function collection(Collection $collection)
    {
        Log::info("Inicio de importacion de Solicitudes Financiera");

        $h = new FLU_Homologacion();

        $errores = [];

        $this->contadorRegistro = $this->carga->RegistrosCargados ?? 0;
        $contErrores = $this->carga->RegistrosFallidos ?? 0;
        $idCarga = $this->carga->ID;

        // Seccion Preguardado --------------------------------------------------
//        $datosPrevios = VT_Salvin::all();
//        VT_Salvin::query()->delete();

        /*   $datos = [
               "id_roma" => null,
               "financiera_origen" => "NTFS",
               "estado" => "sin_enviar_a_evaluar",
               "fecha_cotizacion" => 45635,
               "sucursal" => "P. SUR I",
               "vendedor" => "FRANCISCO GABRIEL VALENZUELA SANCHEZ",
               "nombre_ejecutivo" => "DAVID GERMAN CONCHA ZAMORANO",
               "rut_cliente" => "17908760-K",
               "nombre_cliente" => "YASNA MACARENA NÚÑEZ COLLAO",
               "email_cliente" => "YASNITA1632@GMAIL.COM",
               "telefono_cliente" => "948748878",
               "marca" => "NISSAN",
               "modelo" => "VERSA",
               "producto" => "Credinissan Plus Credimás",
               "tipo_credito" => "Credinissan Plus",
               "nuevo_usado" => "Nuevo",
               "tipocreditoid" => 2,
               "idsucursal" => 31,
               "idestado" => 1,
               "idmarca" => 3,
               "idmodelo" => 57,
               "idcanal" => 3,
           ];*/


        VT_CotizacionesSolicitudesCredito::whereBetween('FechaCotizacion', [$this->fecha_inicio, $this->fecha_fin])
            ->delete();

        // Seccion de importacion --------------------------------------------------
        foreach ($collection as $record) {
//            dd($record);
            if ($this->contadorRegistro > 0) {

                $idFlujo = 24; // Carga Financieras


//                $marcaID = MA_Marcas::where('Marca', $record['marca'])->first()->ID ?? 1;
//                $modeloID = $h->getDato($record['modelo'], $idFlujo, 'modelo', 0);
//                $productoID = $h->getDato($record['producto'], $idFlujo, 'producto', 0);
//                $estadoID = $h->getDato($record['estado'], $idFlujo, 'estado', 0);

                /*   $vendedorID = MA_Usuarios::where('Nombre', $record['vendedor'])->first()->ID ?? 1;

                   $rut = $record['rut_cliente'];
                   $rut = substr($rut, 0, strlen($rut) - 1) . "-" . substr($rut, -1, 1);

                   $clienteID = MA_Clientes::where('Rut', $rut)->first()->ID ?? 1;

                   if($vendedorID == 0){
                       $errores[] = ["message" => "No se encontro vendedor",
                           "record" => $record];
                       continue;
                   }

                   if($clienteID == 0){
                       $errores[] = ["message" => "No se encontro cliente",
                           "record" => $record];
                       continue;
                   }
                   */

                // Limpiar el RUT
                $rut = $record['rut_cliente'];
                $rutLimpio = preg_replace('/[.\-]/', '', $rut);

                $clienteID = MA_Clientes::where('Rut', $rutLimpio)->first()->ID ?? 1;

                if ($clienteID == 1) {
                    $clienteObj = MA_Clientes::create([
                        'FechaCreacion' => Carbon::now(),
                        'EventoCreacionID' => 126,
                        'UsuarioCreacionID' => 791,
                        'Nombre' => $record['nombre_cliente'],
                        'Rut' => $rutLimpio,
                        'Email' => $record['email_cliente'],
                        'Telefono' => $record['telefono_cliente']
                    ]);

                    $clienteID = $clienteObj->ID;

                }

                $vendedorExcel = $record['vendedorid'];
                $sucursalExcel = $record['idsucursal'];


                // Segun Canal setea origen y subOrigen
                $canal = $record['idcanal'];
                $financiera = $record['financiera_origen'];

                if ($canal == 1) {
                    $origenid = 14;
                    $suborigenid = 73;
                } else if ($canal == 2 || $financiera == 'AMICAR') {
                    $origenid = 13;
                    $suborigenid = 81;
                } else if ($canal == 2 || $financiera == 'Forum') {
                    $origenid = 13;
                    $suborigenid = 77;
                } else if ($canal == 2 || $financiera == 'Santander') {
                    $origenid = 13;
                    $suborigenid = 82;
                } else if ($canal == 2 || $financiera == 'NTFS') {
                    $origenid = 13;
                    $suborigenid = 80;
                } else if ($canal == 3) {
                    $origenid = 15;
                    $suborigenid = 53;
                } else {
                    $origenid = 9999;
                    $suborigenid = 9999;
                }


                if (is_numeric($record['fecha_cotizacion'])) {
                    $fechaCotizacion = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($record['fecha_cotizacion']);
                } else {
                    $fechaCotizacion = Carbon::createFromFormat("d-m-Y", $record['fecha_cotizacion'])->format('Y-m-d');
                }
//                Log::info("registro : ". $this->contadorRegistro . " ". $record['fecha_cotizacion']);
                /*
                                "financiera_origen" => "NTFS"
                    "estado" => "rechazado_en_evaluacion"
                    "fecha_cotizacion" => 45242
                    "sucursal" => "BILBAO"
                    "vendedor" => "ADAM SANTELICES"
                    "nombre_ejecutivo" => "NICOLAS MUÑOZ"
                    "rut_cliente" => "76202758-5"
                    "nombre_cliente" => "FCR CHILE CERTIFICACIONES SPA  "
                    "email_cliente" => "CRISTIAN.CESLSI@FCRDAS.CL"
                    "telefono_cliente" => "962091463"
                    "marca" => "NISSAN"
                    "modelo" => "NAVARA"
                    "producto" => "Renovación Plus Crediexpert"
                    "tipo_credito" => "Credinissan Plus"
                    "nuevo_usado" => "Nuevo"
                    "tipocreditoid" => 2
                    "idsucursal" => 13
                    "idestado" => 3
                    "idmarca" => 3
                    "idmodelo" => 59
                    "idcanal" => 3
                    "vendedorid" => 3207*/

                Log::info("Procesando linea : " . $this->contadorRegistro);
                // Preparacion de datos desde CSV ------------------------------------------
                $registro = [
                    'Financiera' => $record['financiera_origen'],
                    'Estado' => $record['estado'],
                    'FechaCotizacion' => $fechaCotizacion,
                    'Sucursal' => $record['sucursal'],
                    'Vendedor' => $record['vendedor'],
                    'NombreEjecutivo' => $record['nombre_ejecutivo'],
                    'RutCliente' => $rutLimpio,
                    'NombreCliente' => $record['nombre_cliente'],
                    'EmailCliente' => $record['email_cliente'],
                    'TelefonoCliente' => $record['telefono_cliente'],
                    'Marca' => $record['marca'],
                    'Modelo' => $record['modelo'],
                    'Producto' => $record['producto'],
                    'TipoCredito' => $record['tipo_credito'],

                    'NuevoUsado' => $record['nuevo_usado'],
                    'TipoCreditoID' => $record['tipocreditoid'],
                    'SucursalID' => $record['idsucursal'],
                    'EstadoID' => $record['idestado'],
                    'MarcaID' => $record['idmarca'],
                    'ModeloID' => $record['idmodelo'],

                    'VendedorID' => $record['vendedorid'], // agregar vendedor en excel (nuevo cruce)
                    'ClienteID' => $clienteID,

                    'Cargado' => 0,
                    'VendedorEnSucursal' => 0,
                    'CanalID' => $record['idcanal'],
                    'OrigenID' => $origenid,
                    'SubOrigenID' => $suborigenid,

                    'Concat' => $rutLimpio . $record['idsucursal'] . $record['vendedorid'] . $record['idmodelo'],

                ];

                $varIDSolCredito = VT_CotizacionesSolicitudesCredito::create($registro);
                $varIDSolCredito->fresh();
//                dd($varIDSolCredito);


                $CountSucursal = SIS_UsuariosSucursales::where('Activo', 1)->where('UsuarioID', $vendedorExcel)->where('SucursalID', $sucursalExcel)->count();
                if ($CountSucursal > 0) {
                    $varIDSolCredito->VendedorEnSucursal = 1;
                } else {
                    $varIDSolCredito->VendedorEnSucursal = 0;
                }


                $RutMarca = VT_Cotizaciones::where('ClienteID', $clienteID)
                    ->where('MarcaID', $record['idmarca'])
                    ->whereBetween('FechaCotizacion', [$this->fecha_inicio, $this->fecha_fin])->count();
                if ($RutMarca > 0) {
                    $varIDSolCredito->RutMarca = 1;
                } else {
                    $varIDSolCredito->RutMarca = 0;
                }

                $RutMarcaSucursal = VT_Cotizaciones::where('ClienteID', $clienteID)
                    ->where('MarcaID', $record['idmarca'])
                    ->where('SucursalID', $record['idsucursal'])
                    ->whereBetween('FechaCotizacion', [$this->fecha_inicio, $this->fecha_fin])->count();
                if ($RutMarcaSucursal > 0) {
                    $varIDSolCredito->RutMarcaSucursal = 1;
                } else {
                    $varIDSolCredito->RutMarcaSucursal = 0;
                }

                $RutMarcaSucursalVendedor = VT_Cotizaciones::whereHas('cliente', function ($query) use ($rutLimpio) {
                    $query->where('Rut', $rutLimpio);
                })
                    ->where('MarcaID', $record['idmarca'])
                    ->where('SucursalID', $record['idsucursal'])
                    ->where('VendedorID', $record['vendedorid'])
                    ->whereBetween('FechaCotizacion', [$this->fecha_inicio, $this->fecha_fin])->count();

                if ($RutMarcaSucursalVendedor > 0) {
                    $varIDSolCredito->VendedorEnSucursal = 1;
                } else {
                    $varIDSolCredito->VendedorEnSucursal = 0;
                }

                // --- ActualizaCot
                $ActualizaCot = VT_Cotizaciones::whereHas('cliente', function ($query) use ($rutLimpio) {
                    $query->where('Rut', $rutLimpio);
                })
                    ->where('MarcaID', $record['idmarca'])
                    ->where('SucursalID', $record['idsucursal'])
                    ->where('VendedorID', $record['vendedorid'])
                    ->whereBetween('FechaCotizacion', [$this->fecha_inicio, $this->fecha_fin])->get();
                if ($ActualizaCot) {
                    $varIDSolCredito->RutMarcaSucursal = 1;// ??
                } else {
                    $varIDSolCredito->RutMarcaSucursal = 0;
                }


                $RutMarcaSucursalVendedor = VT_Cotizaciones::whereHas('cliente', function ($query) use ($rutLimpio) {
                    $query->where('Rut', $rutLimpio);
                })
                    ->where('MarcaID', $record['idmarca'])
                    ->where('SucursalID', $record['idsucursal'])
                    ->where('VendedorID', $record['vendedorid'])
                    ->whereBetween('FechaCotizacion', [$this->fecha_inicio, $this->fecha_fin])->count();
                if ($RutMarcaSucursalVendedor) {
                    $varIDSolCredito->RutMarcaSucursalVendedor = 1;
                    //  VT_CotizacionesSolicitudesCredito::where('RutCliente',$RutMarca(0)->Rut)->where('MarcaID',$RutMarca(0)->MarcaID)->update(['RutMarca'=>1]);
                } else {
                    $varIDSolCredito->RutMarcaSucursalVendedor = 0;
                }

                $RutMarcaVendedor = VT_Cotizaciones::whereHas('cliente', function ($query) use ($rutLimpio) {
                    $query->where('Rut', $rutLimpio);
                })
                    ->where('MarcaID', $record['idmarca'])
                    ->where('VendedorID', $record['vendedorid'])
                    ->whereBetween('FechaCotizacion', [$this->fecha_inicio, $this->fecha_fin])->count();
                if ($RutMarcaVendedor) {
                    $varIDSolCredito->RutMarcaVendedor = 1;
                    //  VT_CotizacionesSolicitudesCredito::where('RutCliente',$RutMarca(0)->Rut)->where('MarcaID',$RutMarca(0)->MarcaID)->update(['RutMarca'=>1]);
                } else {
                    $varIDSolCredito->RutMarcaVendedor = 0;
                }

                // GUARDA los cambios
                $varIDSolCredito->save();


                $ActualizaCot = VT_Cotizaciones::whereHas('cliente', function ($query) use ($rutLimpio) {
                    $query->where('Rut', $rutLimpio);
                })
                    ->where('MarcaID', $record['idmarca'])
                    ->where('SucursalID', $record['idsucursal'])
                    ->where('VendedorID', $record['vendedorid'])
                    ->whereBetween('FechaCotizacion', [$this->fecha_inicio, $this->fecha_fin])->get();

                foreach ($ActualizaCot as $data) {
                    $data->EstadoID = $record['idestado'];
                    $data->CotExterna = 1;
                    $data->SolicitudCredito = 1;
                    $data->FechaActualizacion = Carbon::now();
                    $data->EventoActualizacionID = 126;
                    $data->UsuarioActualizacionID = 791;
                    $data->Bandera = 'A';

                    Log::info("Actualizando Cotizacion : " . $data->ID);

                    $data->save();
                    //  VT_CotizacionesSolicitudesCredito::where('RutCliente',$RutMarca(0)->Rut)->where('MarcaID',$RutMarca(0)->MarcaID)->update(['RutMarca'=>1]);
                }


                //  VT_CotizacionesSolicitudesCredito::where('RutCliente',$RutMarca(0)->Rut)->where('MarcaID',$RutMarca(0)->MarcaID)->update(['RutMarca'=>1]);
//            }


                $registros[] = $registro;

            } // Fin contador registro
            $this->contadorRegistro++;

        } // Fin foreach

        $json = json_encode($registros);
        $errorJson = json_encode($errores);
        Storage::put(storage_path('app/public/financieras' . $this->contadorRegistro . '.json'), $json);
        Storage::put(storage_path('app/public/financierasErrors' . $this->contadorRegistro . '.json'), $errorJson);

        // paso final --------------------------------------------------

        $contErrores += count($errores);
        $this->carga->Registros = $contErrores + $this->contadorRegistro;
        $this->carga->RegistrosCargados = $this->contadorRegistro;
        $this->carga->RegistrosFallidos = $contErrores;

        if ($this->contadorRegistro) {
            $this->carga->Estado = 2;
        } else {
            $this->carga->Estado = 3;
        }
        $this->carga->save();

        Log::info("Fin de importacion de Solicitudes Financieras");
        return true;
    }

    public
    function chunkSize(): int
    {
        return 1000;
    }

    public
    function batchSize(): int
    {
        return 1000;
    }
}
