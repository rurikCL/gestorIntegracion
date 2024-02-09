<?php

namespace App\Imports;

use App\Models\MA\MA_Clientes;
use App\Models\MA\MA_Marcas;
use App\Models\MA\MA_Modelos;
use App\Models\MA\MA_Sucursales;
use App\Models\MA\MA_Usuarios;
use App\Models\TDP\TDP_Cotizaciones;
use App\Models\TDP\TDP_CotizacionesEstados;
use App\Models\TDP\TDP_EjecutivosCotizaciones;
use App\Models\TDP\TDP_SucursalesFinex;
use App\Models\TDP\TDP_UsuariosCotizaciones;
use App\Models\TDP\TDP_WebPompeyoSucursales;
use App\Models\VT\VT_Cotizaciones;
use App\Models\VT\VT_CotizacionesTipoCredito;
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
use mysql_xdevapi\Result;

class CotizacionesForumImport implements ToCollection, WithChunkReading, WithBatchInserts, ShouldQueue
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
        Log::info("Inicio de importacion de cotizaciones Forum");

        // Logica para importar cotizaciones
        $errores = [];

        $this->contadorRegistro = $this->carga->RegistrosCargados ?? 0;
        $contErrores = $this->carga->RegistrosFallidos ?? 0;
        $idCarga = $this->carga->ID;

        Log::info("Registros cargados " . $this->carga->RegistrosCargados);
        Log::info("Registros fallidos " . $this->carga->RegistrosFallidos);


        foreach ($collection as $record) {
            if ($this->contadorRegistro > 0) {
                if ($record[0] == "Total") {
                    break;
                }

                // Preparacion de datos desde CSV ------------------------------------------
                $seguimiento = $record[0];
                $estado = trim(str_replace(" ", "", $record[1]));
                $rut = str_replace(".", "", str_replace(" ", "", str_replace("-", "", $record[2])));
                $nombre = $record[3];
                $marcaNombre = $record[4];
                $modeloNombre = $record[5];
                $fechaCotizacion = $record[6];
                $fechaPrecurse = $record[7];
                $fechaCurse = $record[8];
                $sucursalNombre = $record[9];
                $vendedorNombre = $record[10];
                $ejecutivoNombre = $record[11];
                $tipoVehiculo = $record[12];
                $productoBase = $record[13];
                $promocion = $record[14];
                $plazo = $record[15];
                $seguro = $record[16];

                $producto = 1;

                $fechaConcat = Carbon::createFromFormat("Y-m-d", $fechaCotizacion)->format('Ym');
                $fechaCotizacion = Carbon::createFromFormat("Y-m-d", $fechaCotizacion)->format('Y-m-d');


                // seccion marca --------------------------------------------------
                $marcaSplit = explode(" ", $marcaNombre);
                $marca = MA_Marcas::where('Marca', 'like', $marcaSplit[0])->first();
                if (!$marca) {
                    $marca = 1;
                } else {
                    $marca = $marca->ID;
                }


                // seccion de modelo --------------------------------------------------
                $modelo = MA_Modelos::where('Modelo', 'like', $modeloNombre)->first();
                if (!$modelo) {
//                    $errores[$numeroOperacion]['modelo'] = 'No se encontro el modelo';
                    $modelo = 1;
                } else {
                    $modelo = $modelo->ID;
                }


                // seccion cliente --------------------------------------------------
                $cliente = MA_Clientes::where('Rut', $rut)->first();
                if (!$cliente) {

                    // creacion de cliente
                    $cliente = MA_Clientes::create([
                        'Rut' => $rut,
                        'Nombre' => $nombre,
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
                        $errores[$this->contadorRegistro]['cliente'] = 'No se pudo crear el cliente';
                        $cliente = 1;
                    }

                } else {
                    $cliente = $cliente->ID;
                }


                // seccion sucursal --------------------------------------------------
//                Log::info("buscando sucursal " . $sucursalNombre);
                $sucursalObj = MA_Sucursales::where('Sucursal', 'like', $sucursalNombre)->first();
                if (!$sucursalObj) {

//                    Log::info("buscando sucursal en finex " . $sucursalNombre);
                    $tdpSucursal = TDP_SucursalesFinex::where('NombreFinex', $sucursalNombre)->first();
                    if ($tdpSucursal) {
//                        Log::info("sucursal finex encontrada " . $tdpSucursal->Sucursal);
                        $sucursalID = $tdpSucursal->IDSucursal;
                    } else {
                        $sucursalID = 1;
                    }

                } else {
//                    Log::info("sucursal encontrada " . $sucursalObj->Sucursal);
                    $sucursalID = $sucursalObj->ID;
                }


                // seccion vendedor --------------------------------------------------
//                Log::info("buscando vendedor " . $vendedorNombre);
                $vendedor = MA_Usuarios::where('Nombre', 'like', $vendedorNombre)->first();
                if (!$vendedor) {
                    $vendedorAux = TDP_UsuariosCotizaciones::where('NombreRoma', $vendedorNombre)->first();
                    if ($vendedorAux) {
                        $vendedor = $vendedorAux->IDRoma;
                    } else {
                        $vendedor = 1;
//                    $errores[$numeroOperacion]['vendedor'] = 'No se encontro el vendedor';
                    }
                } else {
//                    Log::info("vendedor encontrado " . $vendedor->Nombre);
                    $vendedor = $vendedor->ID;
                }


                // seccion ejecutivo --------------------------------------------------
//                Log::info("buscando ejecutivo " . $ejecutivoNombre);
                $ejecutivo = MA_Usuarios::where('Nombre', 'like', $ejecutivoNombre)->first();
                if (!$ejecutivo) {
                    $ejecutivoAux = TDP_EjecutivosCotizaciones::where('NombreRoma', $ejecutivoNombre)->first();
                    if ($ejecutivoAux) {
                        $ejecutivo = $ejecutivoAux->IDRoma;
                    } else {
                        $ejecutivo = 1;
//                    $errores[$numeroOperacion]['ejecutivo'] = 'No se encontro el ejecutivo';

                    }
                } else {
//                    Log::info("ejecutivo encontrado " . $ejecutivo->Nombre);
                    $ejecutivo = $ejecutivo->ID;
                }


                // seccion producto --------------------------------------------------
                if ($productoBase == 'Convencional') {
                    $producto = 1;
                } else if ($productoBase == 'Credinissan plus') {
                    $producto = 2;
                }
                $productoCredito = VT_CotizacionesTipoCredito::where('TipoCredito', $productoBase)->first();
                if ($productoCredito) {
                    $productoID = $productoCredito->ID;
                } else {
                    $productoID = $producto;
                }

                // seccion estado --------------------------------------------------
                $estado = TDP_CotizacionesEstados::where('IDAnterior', $estado)->first();
                if ($estado) {
                    $estadoNuevo = $estado->IDNuevo;
                } else {
                    $estadoNuevo = 1; // verificar valor por defecto
                }


                // seccion guardado --------------------------------------------------
                if (!isset($errores[$this->contadorRegistro])) {

                    $concatenacion = $cliente . $sucursalID . $fechaConcat;

                    $data = [
                        'seguimiento' => $seguimiento,
                        'estado' => $estadoNuevo,
                        'rut' => $rut,
                        'cliente' => $cliente,
                        'marca' => $marca,
                        'modelo' => $modelo,
                        'fecha_cotizacion' => $fechaCotizacion,
                        'fecha' => $fechaPrecurse,
                        'fecha_cursada' => $fechaCurse,
                        'sucursal' => $sucursalID,
                        'vendedor' => $vendedor,
                        'ejecutivo' => $ejecutivo,
                        'tipo_vehiculo' => $tipoVehiculo,
                        'producto' => $productoID,
                        'promocion' => $promocion,
                        'plazo' => $plazo,
                        'seguro' => $seguro,
                        'forma_pago' => 'Pago Cliente',
                        'accesorios' => 0,
                        'cotizados_nissan' => 0,
                        'solicitados_nissan' => 0,
                        'aprobados_nissan' => 0,
                        'cursados_nissan' => 0,
                        'ForumTanner' => 0,
                        'ConcatExterna' => $concatenacion,
                        'ClienteID' => $cliente,
                        'Cargado' => 0,
                        'id_cotizacion_pompeyo' => 0,
//                            'Fecha_Carga' => Carbon::now()->format('Y-m-d H:i:s'),
//                            'Cargado2' => 0,
//                            'ConcatAnterior' => null,
                    ];

//                    $resutlado = TDP_Cotizaciones::create($data);

                    $resultado = VT_Cotizaciones::firstOrCreate(
                        [
                            'ClienteID' => $cliente,
                            'SucursalID' => $sucursalID,
                            'FechaCotizacion' => $fechaCotizacion
                        ],
                        [
                            'FechaCreacion' => Carbon::now()->format("Y-m-d H-i-s"),
                            'EventoCreacionID' => 126,
                            'UsuarioCreacionID' => 2567,
                            'FechaCotizacion' => $fechaCotizacion,
                            'SucursalID' => $sucursalID,
                            'VendedorID' => $vendedor,
                            'EjecutivoFI' => $ejecutivo,
                            'CanalID' => 1,
                            'ClienteID' => $cliente,
                            'OrigenID' => 1,
                            'SubOrigenID' => 1,
                            'EstadoID' => $estadoNuevo,
                            'MarcaID' => $marca,
                            'ModeloID' => $modelo,
                            'VersionID' => 1,
                            'Anno' => 0,
                            'Cantidad' => 1,
                            'ValorVehiculo' => 0,
                            'TipoCreditoID' => $productoID,
                            'CantidadCuotas' => 0,
                            'MetodoPago' => 0,
                            'Pie' => 0,
                            'Retoma' => 0,
                            'TasaInteres' => 0,
                            'GastosOperacionales' => 0,
                            'AdicionalesTotal' => 0,
                            'SimulacionCuotaIDExterno' => 0,
                            'ValorCuota' => 0,
                            'VFMG' => 0,
                            'Preevaluacion' => 0,
                            'SeguroDegravamen' => 0,
                            'SeguroCesantia' => 0,
                            'Testdrive' => 0,
                            'Aval' => 0,
                            'AvalClienteID' => 1,
                            'ConcatID' => $concatenacion,
                            'Agendado' => 0,
                            'LeadID' => 0,
                            'SolCreditoIDExterno' => 0,
                            'FinancieraID' => 1,
                            'RenovacionID' => 0,
                            'Venta' => 0,
                            'Vendido' => 0,
                            'ForumTanner' => 0,
                            'CotExterna' => 1,
                            'FechaCarga' => Carbon::now()->format("Y-m-d H-i-s"),
                            'Bandera' => 'N',
                            'IDCarga' => $idCarga,
                            'ConcatNuevo' => $concatenacion,
                            'ConcatAnterior' => $concatenacion,
                        ]
                    );

                    if (!$resultado->wasRecentlyCreated) {
                        $resultado->Bandera = 'A';
                        $resultado->IDCarga = $idCarga;
                        $resultado->EstadoID = $estadoNuevo;
                        $resultado->FechaCarga = Carbon::now()->format("Y-m-d H-i-s");
                        $resultado->FechaActualizacion = Carbon::now()->format("Y-m-d H-i-s");
                        $resultado->EventoActualizacionID = 126;
                        $resultado->UsuarioActualizacionID = 2567;
                        $resultado->save();
                    }

                    if ($resultado) {
                        Log::info('Se guardo el registro ' . $this->contadorRegistro);
                    } else {
                        $errores[$this->contadorRegistro] = 'No se pudo guardar el registro';
                    }
                } else {
                    Log::error("Error en el registro " . $this->contadorRegistro);
                }

            }
            $this->contadorRegistro++;
        }

        // paso final --------------------------------------------------

        /*TDP_Cotizaciones::join('VT_Cotizaciones', 'VT_Cotizaciones.ConcatAnterior', 'TDP_Cotizaciones.ConcatExterna')
            ->where('VT_Cotizaciones.IDCarga', $idCarga)
            ->where('VT_Cotizaciones.ConcatAnterior', '<>', null)
            ->update(['Cargado' => 1]);*/

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
