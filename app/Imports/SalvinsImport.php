<?php

namespace App\Imports;

use App\Models\MA\MA_Clientes;
use App\Models\MA\MA_Marcas;
use App\Models\MA\MA_Modelos;
use App\Models\MA\MA_Sucursales;
use App\Models\MA\MA_Usuarios;
use App\Models\SIS\SIS_UsuariosSucursales;
use App\Models\TDP\TDP_SucursalesFinex;
use App\Models\VT\VT_Salvin;
use App\Models\VT\VT_SalvinComentarios;
use App\Models\VT\VT_Ventas;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class SalvinsImport implements ToCollection, WithBatchInserts, WithCustomCsvSettings
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
        Log::info("Inicio de importacion de Salvin");

        $errores = [];

        $this->contadorRegistro = $this->carga->RegistrosCargados ?? 0;
        $contErrores = $this->carga->RegistrosFallidos ?? 0;
        $idCarga = $this->carga->ID;

        // Seccion Preguardado --------------------------------------------------
        $datosPrevios = VT_Salvin::all();
        VT_Salvin::query()->delete();

        // Seccion de importacion --------------------------------------------------
        foreach ($collection as $record) {
            try {
                if ($this->contadorRegistro > 0) {

                    // Preparacion de datos desde CSV ------------------------------------------

                    // Solo debe considerar registros de saldos de vehiculos
                    $categoria = $record[27];
                    if ($categoria == '1 SALDOS DE VEHICULOS') {

//                    Log::info(print_r($record, true));

                        $marcaNombre = $record[0];
                        $modeloNombre = $record[1];
                        $cajon = $record[2];
                        $nNota = $record[3];
                        $clienteNombre = strlen($record[4] > 100) ? substr($record[4], 0, 99) : $record[4];
                        $estadoVenta = $record[5];
                        $fechaVenta = $record[6];
                        $fechaVto = $record[7];
                        $nDocumento = $record[8];
                        $vendedor = $record[9];
                        $sucursal = $record[10];
                        $saldoxDocumentar = str_replace(".", "", $record[11]);
                        $rutCliente = str_replace("-", "", str_replace(".", "", $record[12]));
                        $telefonoCliente = $record[13];
                        $tipoPago = $record[14];
                        $financiera = $record[15];
                        $creditoPompeyo = $record[16];
                        $tipoDocumento = $record[17];
                        $dias = $record[18];
                        $status = $record[19]; // Tramo
                        $estadoEntrega = $record[20];
                        $sucursales = $record[21];
                        $empresa = $record[22];
                        $gerencia = $record[23];
                        $entidad = $record[24];
                        $tipoEntidad = $record[25];
                        $unidades = $record[26];

//                    $estadoPago = $record[31];
//                    $pagoSaldos = $record[33];
//                    $entregado = $record[36];
//                    $inscrito = $record[37];
//                    $obsCobranza = $record[38];
//                    $obsSalvin = $record[39];

                        // seccion dias --------------------------------------------------
                        $saldosVigentes = ($dias >= 7) ? 1 : 2;

                        // seccion Tramo --------------------------------------------------
                        $idTramo = 0;
                        switch ($status) {
                            case 'T1':
                                $idTramo = 1;
                                break;
                            case 'T2':
                                $idTramo = 2;
                                break;
                            case 'T3':
                                $idTramo = 3;
                                break;
                            case 'POR VENCER':
                                $idTramo = 0;
                                break;
                        }

                        // secccion venta cajon --------------------------------------------------
                        $cajonObj = VT_Ventas::where('Cajon', $cajon)
                            ->where('EstadoVentaID', 4)
                            ->orderBy('ID', 'desc')
                            ->first();

                        // seccion marca --------------------------------------------------
                        $marcaSplit = explode(" ", $marcaNombre);
                        if (count($marcaSplit) > 1 && $marcaSplit[0] != 'KIA') {
                            Log::info("Marca con mas de una palabra " . $marcaNombre . " Split : " . print_r($marcaSplit, true));
                        }

                        $marca = MA_Marcas::where('Marca', 'like', "%" . $marcaSplit[0] . "%")->first();
                        if (!$marca) {
                            $marcaID = 1;
                        } else {
                            $marcaID = $marca->ID;
                        }


                        // seccion de modelo --------------------------------------------------
                        $modelo = MA_Modelos::where('Modelo', 'like', $modeloNombre)->first();
                        if (!$modelo) {
//                    $errores[$numeroOperacion]['modelo'] = 'No se encontro el modelo';
                            $modeloID = 1;
                        } else {
                            $modeloID = $modelo->ID;
                        }


                        // seccion cliente --------------------------------------------------
                        $telefonoCliente = '';
                        $cliente = MA_Clientes::where('Rut', $rutCliente)->first();
                        if (!$cliente) {

                            // creacion de cliente
                            $cliente = MA_Clientes::create([
                                'Rut' => $rutCliente,
                                'Nombre' => $clienteNombre,
                                'Telefono' => '',
                                'FechaCreacion' => Carbon::now()->format('Y-m-d H:i:s'),
                                'EventoCreacionID' => 1,
                                'UsuarioCreacionID' => 1,
                                'Email' => '',
                                'Direccion' => '',
                            ]);

                            if ($cliente) {
                                Log::info("cliente creado " . $cliente->Nombre);
                                $clienteID = $cliente->ID;
                            } else {
                                $errores[$this->contadorRegistro]['cliente'] = 'No se pudo crear el cliente';
                                $clienteID = 1;
                            }

                        } else {
                            $clienteID = $cliente->ID;
                            $telefonoCliente = $cliente->Telefono;
                        }


                        // seccion sucursal --------------------------------------------------
//                Log::info("buscando sucursal " . $sucursalNombre);
                        if ($cajonObj) {
                            $sucursalID = $cajonObj->SucursalID;
                        } else {
                            $sucursalObj = MA_Sucursales::where('Sucursal', 'like', $sucursal)->first();
                            if (!$sucursalObj) {

                                $tdpSucursal = TDP_SucursalesFinex::where('NombreFinex', $sucursal)->first();
                                if ($tdpSucursal) {
                                    $sucursalID = $tdpSucursal->IDSucursal;
                                } else {
                                    $sucursalID = 1;
                                }

                            } else {
//                    Log::info("sucursal encontrada " . $sucursalObj->Sucursal);
                                $sucursalID = $sucursalObj->ID;
                            }
                        }


                        // seccion vendedor --------------------------------------------------
//                Log::info("buscando vendedor " . $vendedorNombre);
                        if ($cajonObj) {
                            $vendedorID = $cajonObj->VendedorID;
                        } else {
                            $vendedor = str_replace(" .", "", $vendedor);
                            $vendedorObj = MA_Usuarios::where('Nombre', 'like', $vendedor)->first();
                            if (!$vendedorObj) {
                                $vendedorID = 1;
                            } else {
                                $vendedorID = $vendedorObj->ID;
                            }
                        }


                        // seccion JefeSucursal --------------------------------------------------

                        $jefeSucursalObj = MA_Usuarios::jefeSucursal()
                            ->SucursalAsignada($sucursalID)
                            ->first();
                        $jefeSucursalID = $jefeSucursalObj->ID ?? 1;

                        // seccion Ultimo comentario --------------------------------------------------
                        $comentario = VT_SalvinComentarios::where('Cajon', $cajon)
                            ->orderBy('ID', 'desc')->first();
                        $comentario = $comentario->Comentario ?? '';


                        // seccion guardado --------------------------------------------------
                        if (!isset($errores[$this->contadorRegistro])) {
                            $data = [
                                'Marca' => $marcaID,
                                'Modelo' => $modeloNombre,
                                'Cajon' => $cajon,
                                'Cliente' => $clienteNombre,
                                'ClienteRut' => $rutCliente,
                                'Telefono' => $telefonoCliente,
                                'Estado' => $estadoVenta,
                                'FechaVenta' => Carbon::parse($fechaVenta)->format('Y-m-d'),
                                'FechaFactura' => Carbon::parse($fechaVto)->format('Y-m-d'),
                                'Sucursal' => $sucursalID,
                                'Tipo' => 'Saldo por documentar',
                                'Saldo' => $saldoxDocumentar,
                                'Vendedor' => $vendedorID,
                                'JefeSucursal' => $jefeSucursalID,
                                'Comentario' => $comentario,
                                'Timestamp' => null,
                                'FechaEstimado' => null,
                                'TipoEstimado' => null,
                                'Tramo' => $idTramo,
                                'SaldosVigentes' => $saldosVigentes,
                                'FechaActualizacion' => Carbon::now()->format('Y-m-d H:i:s'),
                                'FechaFacturaEst' => null,
                                'Financiera' => $entidad,
                                'TipoVenta' => $tipoEntidad,
                            ];

                            $resultado = VT_Salvin::create($data);

                            if ($resultado) {
//                            Log::info('Se guardo el registro ' . $this->contadorRegistro);
                            } else {
                                $errores[$this->contadorRegistro] = 'No se pudo guardar el registro';
                            }

                        }

                    } // Fin categoria Saldo vehiculo
                    else {
                        // Finalizamos el ciclo
                        break;
                    }

                } // Fin contador registro
                $this->contadorRegistro++;
            } catch (\Exception $e) {
                Log::error("Error en la importacion de Salvin: " . $e->getMessage());
                $errores[$this->contadorRegistro] = $e->getMessage();
            }

        } // Fin foreach

        Log::info("Ejecutando actualizacion posterior (comentarios)");
        foreach ($datosPrevios as $dato) {
            VT_Salvin::where('Cajon', $dato->Cajon)
                ->update([
//                    'Comentario' => $dato->Comentario, // actualizado previamente
                    'Timestamp' => $dato->Timestamp,
                    'FechaEstimado' => $dato->FechaEstimado,
                    'Tipo' => $dato->Tipo
                ]);
        }

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


        Log::info("Fin de importacion de Salvin");

        return true;
    }

    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'ISO-8859-1'
        ];
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
