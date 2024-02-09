<?php

namespace App\Imports;

use App\Models\MA\MA_Clientes;
use App\Models\MA\MA_Modelos;
use App\Models\MA\MA_Sucursales;
use App\Models\MA\MA_Usuarios;
use App\Models\TDP\TDP_Cotizaciones;
use App\Models\TDP\TDP_WebPompeyoSucursales;
use Carbon\Carbon;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class CotizacionesNissanImport implements ToCollection, WithChunkReading, WithBatchInserts, ShouldQueue
{
    private $carga = null;

    use Importable;

    public function __construct($carga)
    {
        $this->carga = $carga;
    }

    public function collection(Collection $collection)
    {
        Log::info("Inicio de importacion de cotizaciones Nissan");

        // Logica para importar cotizaciones
        $contadorRegistro = 0;
        $errores = [];

        foreach ($collection as $record) {
            if ($contadorRegistro > 0) {
                if ($record[0] == "Total") {
                    break;
                }

                $numeroOperacion = $record[0];
                $fechaCotizacion = $record[1];
                $modelo = $record[2];
                $rut = str_replace("-", "", $record[3]);
                $estado = $record[4];
                $version = $record[5];
                $tipoCredito = $record[6];
                $valorVehiculo = $record[7];
                $pie = $record[8];
                $tasa = $record[9];
                $plazo = $record[10];
                $cuota = $record[11];
                $vfmg = $record[12];
                $comision = $record[13];
                $telefono = $record[14];
                $automotora = $record[15];
                $sucursal = $record[16];
                $nombreCliente = $record[17];
                $nombreEjecutivo = $record[18];
                $nombreVendedor = $record[19];
                $cotizados = $record[20];
                $solicitados = $record[21];
                $aprobados = $record[22];
                $cursados = $record[23];

                $fechaConcat = Carbon::createFromFormat("d-m-Y", $fechaCotizacion)->format('Ymd');
                $fechaCotizacion = Carbon::createFromFormat("d-m-Y", $fechaCotizacion)->format('Y-m-d');


                // seccion de modelo
                $modelo = MA_Modelos::where('Modelo', 'like', $modelo)->first();
                if (!$modelo) {
//                    $errores[$numeroOperacion]['modelo'] = 'No se encontro el modelo';
                    $modelo = 1;
                } else {
                    $modelo = $modelo->Modelo;
                }

                $cliente = MA_Clientes::where('Rut', $rut)->first();
                if (!$cliente) {

                    // creacion de cliente
                    $cliente = MA_Clientes::create([
                        'Rut' => $rut,
                        'Nombre' => $nombreCliente,
                        'Telefono' => $telefono ?? 1,
                        'FechaCreacion' => Carbon::now()->format('Y-m-d H:i:s'),
                        'EventoCreacionID' => 1,
                        'UsuarioCreacionID' => 1,
                        'Email' => '',
                        'Direccion' => '',
                    ]);
                    if ($cliente) {
                        Log::info("cliente creado " . $cliente->Nombre);
                        $cliente = $cliente->ID;
                    } else {
                        $errores[$numeroOperacion]['cliente'] = 'No se pudo crear el cliente';
                        $cliente = 1;
                    }

                } else {
                    $cliente = $cliente->ID;
                }

                Log::info("buscando sucursal " . $sucursal);
                $sucursalObj = MA_Sucursales::where('Sucursal', 'like', $sucursal)->first();
                if (!$sucursalObj) {

                    Log::info("buscando sucursal en tdp " . $sucursal);
                    $tdpSucursal = TDP_WebPompeyoSucursales::where('Sucursal', 'like', $sucursal)->first();
                    if ($tdpSucursal) {
                        Log::info("sucursal tdp encontrada " . $tdpSucursal->Sucursal);
                        $sucursal = $tdpSucursal->SucursalID;
                    } else {
                        $sucursal = 1;
                    }

                } else {
                    Log::info("sucursal encontrada " . $sucursal->Sucursal);
                    $sucursal = $sucursal->ID;
                }

                Log::info("buscando vendedor " . $nombreVendedor);
                $vendedor = MA_Usuarios::where('Nombre', 'like', $nombreVendedor)->first();
                if (!$vendedor) {
                    $vendedor = 1;
//                    $errores[$numeroOperacion]['vendedor'] = 'No se encontro el vendedor';
                } else {
                    Log::info("vendedor encontrado " . $vendedor->Nombre);
                    $vendedor = $vendedor->ID;
                }

                Log::info("buscando ejecutivo " . $nombreEjecutivo);
                $ejecutivo = MA_Usuarios::where('Nombre', 'like', $nombreEjecutivo)->first();
                if (!$ejecutivo) {
                    $ejecutivo = 1;
//                    $errores[$numeroOperacion]['ejecutivo'] = 'No se encontro el ejecutivo';
                } else {
                    Log::info("ejecutivo encontrado " . $ejecutivo->Nombre);
                    $ejecutivo = $ejecutivo->ID;
                }


                if (!isset($errores[$numeroOperacion])) {

                    $concatenacion = $cliente . $sucursal . $fechaConcat;

                    $data = [
                        'seguimiento' => $estado,
                        'estado' => $estado,
                        'rut' => $rut,
                        'cliente' => $nombreCliente,
                        'marca' => 'NISSAN',
                        'modelo' => $modelo,
                        'fecha_cotizacion' => $fechaCotizacion,
                        'sucursal' => $sucursal,
                        'vendedor' => $vendedor,
                        'ejecutivo' => $ejecutivo,
                        'tipo_vehiculo' => 'Nuevo',
                        'producto' => 1,
                        'promocion' => '',
                        'plazo' => $plazo,
                        'seguro' => 'NO',
                        'forma_pago' => 'Pago Cliente',
                        'accesorios' => 0,
                        'cotizados_nissan' => 0,
                        'solicitados_nissan' => 0,
                        'aprobados_nissan' => 0,
                        'cursados_nissan' => 0,
                        'ForumTanner' => 0,
                        'ConcatExterna' => $concatenacion,
                        'ClienteID' => $cliente,
                        'Cargado' => 1,
                        'id_cotizacion_pompeyo' => 0,
//                            'Fecha_Carga' => Carbon::now()->format('Y-m-d H:i:s'),
//                            'Cargado2' => 0,
//                            'ConcatAnterior' => null,
                    ];

                    $resutlado = TDP_Cotizaciones::updateOrCreate([
                        'ConcatExterna' => $concatenacion,
                    ], $data);

                    if ($resutlado) {
                        Log::info('Se guardo el registro ' . $numeroOperacion);
                    } else {
                        $errores[$numeroOperacion] = 'No se pudo guardar el registro';
                    }
                } else {
                    Log::error("Error en el registro " . $numeroOperacion);
                }

            }
            $contadorRegistro++;
        }

        $contErrores = count($errores);
        $this->carga->Registros = $contErrores + $contadorRegistro;
        $this->carga->RegistrosCargados = $contadorRegistro;
        $this->carga->RegistrosFallidos = $contErrores;

        if ($contadorRegistro) {
            $this->carga->Estado = 2;
        } else {
            $this->carga->Estado = 3;
        }
        $this->carga->save();


        Log::error(print_r($errores, true));

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
