<?php

namespace App\Imports;

use AnourValar\EloquentSerialize\Tests\Models\File;
use App\Models\FLU\FLU_Homologacion;
use App\Models\MA\MA_Clientes;
use App\Models\MA\MA_Marcas;
use App\Models\MA\MA_Usuarios;
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

    use Importable;

    public function __construct($carga)
    {
        $this->carga = $carga;
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
                $rut  = $record['rut_cliente'];
                 $rutLimpio = preg_replace('/[.\-]/', '',$rut);

                 $clienteID = MA_Clientes::where('Rut', $rutLimpio)->first()->ID ?? 1;

                 if($clienteID == 1){
                     $clienteObj =  MA_Clientes::create([
                        'FechaCreacion'=> Carbon::now(),
                        'EventoCreacionID'=> 126,
                        'UsuarioCreacionID'=> 791,
                        'Nombre'=> $record['nombre_cliente'],
                        'Rut'=> $rutLimpio,
                        'Email'=> $record['email_cliente'],
                        'Telefono'=> $record['telefono_cliente']      
                    ]);

                    $clienteID = $clienteObj->ID;
                    
                } 

                $vendedorExcel = $record['vendedorid'];
                $sucursalExcel = $record['idsucursal'];
               

                // Segun Canal setea origen y subOrigen 
                $canal = $record['idcanal'];
                $financiera = $record['Financiera_Origen'];

                if($canal == 1){
                    $origenid = 14;
                    $suborigenid = 73;
                }else if($canal == 2 || $financiera == 'AMICAR'){                                                   
                    $origenid = 13;
                    $suborigenid = 81;
                }else if($canal == 2 || $financiera == 'Forum'){    
                    $origenid = 13;
                    $suborigenid = 77;
                }else if($canal == 2 || $financiera == 'Santander'){    
                    $origenid = 13;
                    $suborigenid = 82;
                }else if($canal == 2 || $financiera == 'NTFS'){  
                    $origenid = 13;
                    $suborigenid = 80;
                }else if($canal == 3){
                    $origenid = 15;
                    $suborigenid = 53;
                }else{
                    $origenid = 9999;
                    $suborigenid = 9999;
                }


                if(is_numeric($record['Fecha_Cotizacion'])){
                    $fechaCotizacion = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($record['Fecha_Cotizacion']);
                } else {
                    $fechaCotizacion = Carbon::createFromFormat("d-m-Y",$record['Fecha_Cotizacion'])->format('Y-m-d');
                }
//                Log::info("registro : ". $this->contadorRegistro . " ". $record['fecha_cotizacion']);

                // Preparacion de datos desde CSV ------------------------------------------
                $registro = [
                    'Financiera' => $record['Financiera_Origen'],
                    'Estado' => $record['Estado'],
                    'FechaCotizacion' => $fechaCotizacion,
                    'Sucursal' => $record['Sucursal'],
                    'Vendedor' => $record['Vendedor'],
                    'NombreEjecutivo' => $record['nombre_Ejecutivo'],
                    'RutCliente' => $rutLimpio,
                    'NombreCliente' => $record['nombre_cliente'],
                    'EmailCliente' => $record['email_cliente'],
                    'TelefonoCliente' => $record['telefono_cliente'],
                    'Marca' => $record['Marca'],
                    'Modelo' => $record['Modelo'],
                    'Producto' => $record['producto'],
                    'TipoCredito' => $record['tipo_credito'],

                    'NuevoUsado' => $record['Nuevo_Usado'],
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
                    'SubOrigenID'=> $suborigenid,

                    'Concat' => $rutLimpio . $record['idsucursal'] . $vendedorID . $record['idmodelo'],

                    ];
                  
            $varIDSolCredito =  VT_CotizacionesSolicitudesCredito::create($registro);
//                dd($registro);


            $CountSucursal = SIS_UsuariosSucursales::where('Activo',1)->where('UsuarioID',$vendedorExcel)->where('SucursalID',$sucursalExcel)->count();
        
            if ($CountSucursal > 0){
               // VT_CotizacionesSolicitudesCredito::where('ID',$varIDSolCredito)->update(['VendedorEnSucursal'=>$validaenSucursal]);
                $varIDSolCredito->VendedorEnSucursal = 1;
                $varIDSolCredito->save();
            }else{
                $varIDSolCredito->VendedorEnSucursal = 0;
                $varIDSolCredito->save();
            }

            /*
            
            $RutMarca = VT_Cotizaciones::where('Rut',$rutLimpio)
                                        ->where('MarcaID',$record['idmarca'])
                                        ->whereBetween('FechaCotizacion',[Carbon::now()->FirstOfMonth()->format('d-m-Y'),Carbon::now()->LastOfMonth()->format('d-m-Y')])->count();
                                        */ 

            

            $RutMarca = VT_Cotizaciones::whereHas('cliente',function($query){
                $query->where('Rut',$rutLimpio);
            })
            ->where('MarcaID',$record['idmarca'])
            ->whereBetween('FechaCotizacion',[Carbon::now()->FirstOfMonth()->format('d-m-Y'),Carbon::now()->LastOfMonth()->format('d-m-Y')])->count();

            if($RutMarca){
                $varIDSolCredito->RutMarca = 1;
                $varIDSolCredito->save();
              //  VT_CotizacionesSolicitudesCredito::where('RutCliente',$RutMarca(0)->Rut)->where('MarcaID',$RutMarca(0)->MarcaID)->update(['RutMarca'=>1]);
            }else{
                $varIDSolCredito->RutMarca = 0;
                $varIDSolCredito->save();
            }                           
            //where(date("Y-m"), strtotime('FechaCotizacion')),date("Y-m"), strtotime(Carbon::now());
            

            $RutMarcaSucursal = VT_Cotizaciones::whereHas('cliente',function($query){
                $query->where('Rut',$rutLimpio);
            })
            ->where('MarcaID',$record['idmarca'])
            ->where('SucursalID',$record['idsucursal'])
            ->whereBetween('FechaCotizacion',[Carbon::now()->FirstOfMonth()->format('d-m-Y'),Carbon::now()->LastOfMonth()->format('d-m-Y')])->count();

            if($RutMarcaSucursal){
                $varIDSolCredito->RutMarcaSucursal = 1;
                $varIDSolCredito->save();
              //  VT_CotizacionesSolicitudesCredito::where('RutCliente',$RutMarca(0)->Rut)->where('MarcaID',$RutMarca(0)->MarcaID)->update(['RutMarca'=>1]);
            }else{
                $varIDSolCredito->RutMarcaSucursal = 0;
                $varIDSolCredito->save();
            }  


            $RutMarcaSucursalVendedor = VT_Cotizaciones::whereHas('cliente',function($query){
                $query->where('Rut',$rutLimpio);
            })
            ->where('MarcaID',$record['idmarca'])
            ->where('SucursalID',$record['idsucursal'])
            ->where('VendedorID',$record['vendedorid'])
            ->whereBetween('FechaCotizacion',[Carbon::now()->FirstOfMonth()->format('d-m-Y'),Carbon::now()->LastOfMonth()->format('d-m-Y')])->count();

            if($RutMarcaSucursalVendedor){
                $varIDSolCredito->RutMarcaSucursalVendedor = 1;
                $varIDSolCredito->save();
              //  VT_CotizacionesSolicitudesCredito::where('RutCliente',$RutMarca(0)->Rut)->where('MarcaID',$RutMarca(0)->MarcaID)->update(['RutMarca'=>1]);
            }else{
                $varIDSolCredito->RutMarcaSucursalVendedor = 0;
                $varIDSolCredito->save();
            }  

            $RutMarcaVendedor = VT_Cotizaciones::whereHas('cliente',function($query){
                $query->where('Rut',$rutLimpio);
            })
            ->where('MarcaID',$record['idmarca'])
            ->where('VendedorID',$record['vendedorid'])
            ->whereBetween('FechaCotizacion',[Carbon::now()->FirstOfMonth()->format('d-m-Y'),Carbon::now()->LastOfMonth()->format('d-m-Y')])->count();

            if($RutMarcaVendedor){
                $varIDSolCredito->RutMarcaVendedor = 1;
                $varIDSolCredito->save();
              //  VT_CotizacionesSolicitudesCredito::where('RutCliente',$RutMarca(0)->Rut)->where('MarcaID',$RutMarca(0)->MarcaID)->update(['RutMarca'=>1]);
            }else{
                $varIDSolCredito->RutMarcaVendedor = 0;
                $varIDSolCredito->save();
            } 

            
            $ActualizaCot = VT_Cotizaciones::whereHas('cliente',function($query){
                $query->where('Rut',$rutLimpio);
            })
            ->where('MarcaID',$record['idmarca'])
            ->where('SucursalID',$record['idsucursal'])
            ->where('VendedorID',$record['vendedorid'])
            ->whereBetween('FechaCotizacion',[Carbon::now()->FirstOfMonth()->format('d-m-Y'),Carbon::now()->LastOfMonth()->format('d-m-Y')])->get();

             

                foreach ($ActualizaCot as $data){
                    $data->EstadoID = $record['idestado'];
                    $data->CotExterna = 1;
                    $data->SolicitudCredito = 1;
                    $data->FechaActualizacion = Carbon::now();
                    $data->EventoActualizacionID = 126;
                    $data->UsuarioActualizacionID = 791;
                    $data->Bandera = 'A'; 

                    $data->save();
              //  VT_CotizacionesSolicitudesCredito::where('RutCliente',$RutMarca(0)->Rut)->where('MarcaID',$RutMarca(0)->MarcaID)->update(['RutMarca'=>1]);
            }
           

                $registros[] = $registro;

            } // Fin contador registro
            $this->contadorRegistro++;



        } // Fin foreach

        $json = json_encode($registros);
        $errorJson = json_encode($errores);
        Storage::put(storage_path('app/public/financieras'.$this->contadorRegistro.'.json'), $json);
        Storage::put(storage_path('app/public/financierasErrors'.$this->contadorRegistro.'.json'), $errorJson);


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

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }
}
