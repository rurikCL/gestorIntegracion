<?php

namespace App\Http\Controllers\Flujo;

use App\Http\Controllers\Api\ApiSolicitudController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Logger;
use App\Models\Api\ApiSolicitudes;
use App\Models\FLU\FLU_Flujos;
use App\Models\FLU\FLU_Homologacion;
use App\Models\FLU\FLU_Notificaciones;
use App\Models\MA\MA_Bancos;
use App\Models\MA\MA_Clientes;
use App\Models\MA\MA_IndicadorMonetario;
use App\Models\MA\MA_Marcas;
use App\Models\MA\MA_Modelos;
use App\Models\MA\MA_Usuarios;
use App\Models\MK\MK_Leads;
use App\Models\PV\PV_PostVenta;
use App\Models\SIS\SIS_AutoRedTransaccion;
use App\Models\SIS\SIS_Solicitudes;
use App\Models\Stock;
use App\Models\VT\VT_Cotizaciones;
use App\Models\VT\VT_EstadoResultado;
use App\Models\VT_Ventas;
use Carbon\Carbon;
use Carbon\Traits\Date;
use GuzzleHttp\Client;
use HubSpot\Client\Crm\Contacts\Model\Filter;
use HubSpot\Client\Crm\Deals\Model\Filter as FilterDeal;
use HubSpot\Client\Crm\Deals\Model\FilterGroup;
use HubSpot\Client\Crm\Deals\Model\PublicObjectSearchRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Saloon\XmlWrangler\XmlWriter;
use function Psl\Str\Byte\length;
use HubSpot\Factory;
use HubSpot\Client\Crm\Contacts\ApiException;


class FlujoController extends Controller
{
    //

    public function actualizaStockAPC()
    {
        echo "Ejecutando Flujo Stock APC <br>";
        Log::info("Inicio flujo STOCK APC");

        $flujo = FLU_Flujos::where('Nombre', 'APC_STOCK')->first();

        if ($flujo->Activo) {
            echo ". . . <br>";

            $solicitudCon = new ApiSolicitudController();

            $referencia = $flujo->ID . date("ymdh");

            $req = new Request();
            $req['referencia_id'] = $referencia;
            $req['proveedor_id'] = 7;
            $req['api_id'] = 3;
            $req['prioridad'] = 1;
            $req['flujoID'] = $flujo->ID;
            $req['OnDemand'] = true;

            $resp = $solicitudCon->store($req);
            $resp = $resp->getData();

            $solicitud = ApiSolicitudes::where('id', $resp->id)->first();

            if (substr($solicitud->Respuesta, 0, 4) == 'file') {
                $nombre = substr($solicitud->Respuesta, 5, strlen($solicitud->Respuesta));
                Log::info("Archivo json stock generado " . $nombre);

                $arrayData = json_decode(Storage::get($nombre));
            } else {
                $arrayData = json_decode($solicitud->Respuesta);
            }


            $registros = 0;
            Log::info("Datos a procesar : " . count($arrayData));

            foreach ($arrayData as $data) {

                $stock = Stock::where("Vin", $data->vehiNumero_VIN)->firstOrNew();
                if ($stock) {
                    $stock->Empresa = $data->vehiEmpresa;
                    $stock->Sucursal = $data->vehiSucursal;
//                    $stock->FolioVenta = 0;
//                    $stock->Venta = 0;
//                    $stock->EstadoVenta = '';
//                    $stock->FechaVenta = null;
                    $stock->TipoDocumento = $data->vehiDocumento_Compra;
//                    $stock->Vendedor = $data->;

                    $stock->FechaIngreso = Carbon::parse($data->vehiFecha_Ingreso);
                    $stock->FechaFacturacion = Carbon::parse($data->vehiFecha_Documento);
                    $stock->VIN = $data->vehiNumero_VIN;
                    $stock->Marca = $data->vehiMarca;
                    $stock->Modelo = $data->vehiModelo;
                    $stock->Version = $data->vehiVersion;
                    $stock->CodigoVersion = $data->vehiVersion;
                    $stock->Anno = $data->vehiAño;
//                    $stock->Kilometraje = $data->;
                    $stock->CodigoInterno = $data->vehiCodigo_Interno;
                    $stock->PlacaPatente = $data->vehiPlaca_Patente;
                    $stock->CondicionVehiculo = $data->vehiCondicion;
                    $stock->ColorExterior = $data->vehiColor;
//                    $stock->ColorInterior = $data->;
                    $stock->PrecioVenta = $data->vehiPrecioLista;
                    $stock->EstadoAutoPro = $data->Estado_Autopro;
                    $stock->DiasStock = $data->vehiDiasStock;
                    $stock->EstadoDealer = $data->estado_dealer;
                    $stock->Bodega = $data->vehiBodega;
//                    $stock->Equipamiento = $data->;
                    $stock->NumeroMotor = $data->vehiNumero_Motor;
                    $stock->NumeroChasis = $data->vehiNumero_VIN;
                    $stock->Proveedor = $data->proveedor;
                    $stock->FechaDisponibilidad = Carbon::parse($data->vehiFechaDisponibilidad);
                    $stock->FacturaCompra = $data->vehiNumero_Documento;
//                    $stock->VencimientoDocumento = $data->;
//                    $stock->FechaCompra = $data->;
//                    $stock->FechaVctoRevisionTecnica = $data->;
//                    $stock->NPropietarios = $data->;
//                    $stock->FolioRetoma = 0;
//                    $stock->FechaRetoma = $data->;
//                    $stock->DiasReservado = $data->;
                    $stock->PrecioCompra = $data->vehiCosto_Compra;
                    $stock->Gasto = $data->vehiGastos;
//                    $stock->Accesorios = $data->;
//                    $stock->TotalCosto = $data->;
                    $stock->PrecioLista = $data->vehiPrecioLista;
//                    $stock->Margen = $data->;
//                    $stock->Z = $data->;
//                    $stock->DisponibleENissan = $data->;
//                    $stock->UnidadEspecial = $data->;
//                    $stock->BonoFinanciamiento = $data->;
//                    $stock->BonoMarca = $data->;
//                    $stock->BonoAdicional = $data->;
//                    $stock->DisponibleUsados = $data->;
//                    $stock->Descuento = $data->;

                    $marca = explode(" ", $data->vehiMarca);
                    $marcaID = MA_Marcas::where('Marca', 'like', $marca[0])->first();

                    if ($marcaID) {
                        $stock->MarcaID = $marcaID->ID;
                    } else {
                        Log::error("No se encontro Marca : " . $data->vehiMarca);
                        Log::info("Buscando homologacion");
                        $marcaID = FLU_Homologacion::GetDato(
                            $data->vehiMarca,
                            $flujo->ID,
                            'marca',
                            1
                        );
                    }
                    if ($data->vehiModelo) {
                        $modelo = MA_Modelos::where('Modelo', 'like', $data->vehiModelo)->first() ?? 1;
                        $modeloID = $modelo->ID ?? 1;
                    } else {
                        $modeloID = 1;
                    }
                    $stock->ModeloID = $modeloID;
//                    $stock->VersionID = $data->;

                    $stock->save();

//                    Log::info("Vehiculo " .$data->vehiNumero_VIN . " procesado");

                    $registros++;
                }

            }
            Log::info("Vehiculos procesados");

            FLU_Notificaciones::Notificar($referencia, $flujo->ID);

            echo $registros . " registros guardados";
            echo("<br>" . ($resp->message ?? ''));

        }
    }

    public function sendOtIndumotora()
    {

        echo "Ejecutando Flujo KIA <br>";
        Log::info("Inicio flujo OT Indumotora");

        $flujo = FLU_Flujos::where('Nombre', 'KIA')->first();

        if ($flujo->Activo) {
            $h = new FLU_Homologacion();

            $ordenes = PV_PostVenta::with('venta')
                ->OrdenesKia()
                ->NoNotificado($flujo->ID)
                ->VentasDesde('2022-01-01 00:00:00')
                ->TipoMantencion()
                ->limit($flujo->MaxLote ?? 5)
                ->get();

//            dd($ordenes);
            $cuenta = $ordenes->count();
            $solicitudCon = new ApiSolicitudController();

            foreach ($ordenes as $orden) {
                print PHP_EOL . "Procesando orden : " . $orden->FolioOT . PHP_EOL;
                Log::info("Procesando orden : " . $orden->FolioOT);
                $req = new Request();
                $req['referencia_id'] = $orden->ID;
                $req['proveedor_id'] = 9;
                $req['api_id'] = 11;
                $req['prioridad'] = 1;
                $req['flujoID'] = $flujo->ID;

//            $req['OnDemand'] = true;
                $IdDealer = 55;

                $rut = explode("-", $orden->ClienteRut);
                $rutPagador = explode("-", $orden->ClienteRutPagador);

                $req['data'] = [
                    "row" => [
                        "IDOT" => $orden->FolioOT,
                        "IDOT_madre" => "",
                        "NUM_Fact" => $orden->Folio,
                        "IDDealer" => $IdDealer, // 55
                        "IDSucursalDealer" => $orden->SucursalId, // 37, // enviar homologacion MA_SUCURSALES
                        "FechaApertura" => Carbon::create($orden->FechaOT)->format('Ydm hms'),
                        "HoraApertura" => "000000",
                        "IDClaseServicio" => "Mantencion", //
                        "IDSubClaseServicio" => $orden->Mantencion, // Mantencion
                        "IDModClaseServicio" => "Normal",
                        "Marca" => $orden->Marca,
                        "VIN" => $orden->Vin,
                        "PPU" => $orden->Patente,
                        "Kilometraje" => $orden->Kilometraje,
                        "RutCliente" => $rut[0],
                        "DVCliente" => $rut[1] ?? '',
                        "Propietario" => "Si",
                        "Nombres" => $orden->ClienteNombre,
                        "ApellidoPaterno" => "",
                        "ApellidoMaterno" => "",
                        "Sexo" => "",
                        "EmailParticular" => $orden->ClienteEmail,
                        "TelefonoCelular" => $orden->ClienteTelefono,
                        "Direccion" => $orden->ClienteDireccion,
                        "IDComunaCliente" => $h->getDato($orden->ClienteComuna, $flujo->ID, 'comuna', 86), // default Santiago
                        "ComunaCliente" => $orden->ClienteComuna,
                        "CiudadCliente" => $orden->ClienteCiudad,
                        "IDRegionCliente" => "13",
                        "RegionCliente" => "Region Metropolitana",
                        "TelefonoFijo" => "0",
                        "EstadoOT" => "Facturado",
                        "DescTrabRealizados" => "",
                        "Observaciones" => "",
                        "DetalleOT" => [
                            "item" => [
                                [
                                    "OT" => $orden->FolioOT
                                ]
                            ]
                        ],
                        "DetalleNPF" => [
                            "item" => [
                                /*[
                                    "NPF" => "01",
                                    "IDArtDMSDealer" => "09",
                                    "ClaseArticulo" => "Cl",
                                    "SubClaseArticulo" => "subcl",
                                    "NombreArticulo" => "Name Arti",
                                    "Unidades" => "UN",
                                    "CostoUnitario" => "123.000",
                                    "DescArticulo" => "Des"
                                ],
                                [
                                    "NPF" => "02",
                                    "IDArtDMSDealer" => "10",
                                    "ClaseArticulo" => "Cl",
                                    "SubClaseArticulo" => "subcl",
                                    "NombreArticulo" => "Name Arti",
                                    "Unidades" => "UN",
                                    "CostoUnitario" => "123.000",
                                    "DescArticulo" => "Des"
                                ]*/
                            ]
                        ],
                        "Total" => $orden->TotalNetoFacturado,
                        "DescTotal" => $orden->Dctos,
                        "FechaEntrega" => Carbon::create($orden->FechaOT)->format('Ydm hms'),
                        "HoraEntrega" => "000000",
                        "TipoDocumento" => $orden->TipoOrigen,
                        "FechaEmisionDoc" => Carbon::create($orden->FechaFacturacion)->format('Ydm hms'),
                        "HoraEmisionDoc" => "000000",

                        "IDCiaSeguros" => "no",
                        "CiaSeguros" => "",
                        "Deducible" => "",

                        "RutFacturado" => $rutPagador[0],
                        "DVFacturado" => $rutPagador[1] ?? '',
                        "NombresFacturado" => $orden->ClienteNombrePagador,
                        "ApellidoPaternoFacturado" => "",
                        "ApellidoMaternoFacturado" => "",
                        "EmailFacturado" => $orden->ClienteEmailPagador,
                        "TelefonoCelularFacturado" => $orden->ClienteTelefonoPagador,
                        "DireccionFacturado" => $orden->ClienteDireccionPagador,
                        "IDComunaFacturado" => $comunas[$orden->ClienteComuna] ?? 86,
                        "ComunaFacturado" => $orden->ClienteComunaPagador,
                        "CiudadFacturado" => $orden->ClienteCiudadPagador,
                        "IDRegionFacturado" => "13",
                        "RegionFacturado" => "Metropolitana de Santiago",
                        "TelefonoFijoFacturado" => ""
                    ]
                ];

                $resp = $solicitudCon->store($req);
                echo("<br>" . ($resp->message ?? ''));

            }
        }

        return true;
    }

    public function sendVentasIndumotora()
    {

        echo "Ejecutando Flujo KIA <br>";
        Log::info("Inicio flujo Ventas Indumotora");

        $flujo = FLU_Flujos::where('Nombre', 'KIA SIC')->first();

        if ($flujo->Activo) {
            Log::info("Flujo activo");
//            $h = new FLU_Homologacion();

            $ventas = VT_Ventas::with("modelo", "version", "stock", "cliente", "vendedor", "sucursal")
                ->Gerencia(2)
                ->NoNotificado($flujo->ID)
//                ->where('FechaVenta', '>=', '2023-11-01 00:00:00')
                ->where('FechaVenta', '>=', Carbon::now()->subMonth()->format("Y-m-d 00:00:00"))
                ->where('EstadoVentaID', 4)
                ->where('Cajon', '<>', '')
                ->limit($flujo->MaxLote ?? 5)
                ->get();

            /*$ventas = VT_EstadoResultado::with("modelo", "version", "stock", "cliente", "vendedor", "sucursal", "venta")
                ->Gerencia(2)
                ->NoNotificado($flujo->ID)
//                ->FechaVenta(Carbon::now()->subMonth()->format("Y-m-d 00:00:00"),'>=')
                ->where('FechaDocumento', '>=', Carbon::now()->subMonth()->format("Y-m-d 00:00:00"))
                ->limit($flujo->MaxLote ?? 5)
                ->get();*/

//            dd($ventas);

            if ($ventas) {
                Log::info("Existen ventas");
//                $cuenta = $ventas->count();
                $solicitudCon = new ApiSolicitudController();
                Log::info("Cantidad de ventas : " . count($ventas));

                foreach ($ventas as $venta) {
                    print PHP_EOL . "Procesando orden : " . $venta->ID . PHP_EOL;
                    Log::info("Procesando orden : " . $venta->ID);
                    $req = new Request();
                    $req['referencia_id'] = $venta->ID;
                    $req['proveedor_id'] = 9;
                    $req['api_id'] = 12;
                    $req['prioridad'] = 1;
                    $req['flujoID'] = $flujo->ID;

                    $rut = substr($venta->cliente->Rut, 0, length($venta->cliente->Rut) - 1) . "-" . substr($venta->cliente->Rut, -1);
                    $rutVendedor = substr($venta->vendedor->Rut, 0, length($venta->vendedor->Rut) - 1) . "-" . substr($venta->vendedor->Rut, -1);

                    if ($venta->stock) {
                        if ($venta->stock->modeloID != 1) {
                            $modelo = $venta->stock->modelo->Modelo;
                        } else {
                            $modelo = $venta->stock->Modelo;
                        }

                        if ($venta->stock->versionID != 1) {
                            $version = $venta->stock->version->Version;
                        } else {
                            $version = $venta->stock->Version;
                        }

                        $vin = $venta->stock->VIN ?? $venta->Vin;
                        $color = $venta->stock->ColorExterior ?? $venta->ColorReferencial;
                    } else {
                        $modelo = $venta->modelo->Modelo;
                        $version = $venta->version->Version;
                        $vin = $venta->Vin;
                        $color = $venta->ColorReferencial;
                    }

                    $xml = XmlWriter::make()->write('exportacion', [
                        'venta' => [
                            'codigo_dealers' => 6, // Valor fijo (pompeyo)
                            'marca' => 1, // Codigo para KIA (externo)
                            'modelo' => $modelo,
                            'vin' => $vin,
                            'version' => $version,
                            'color' => $color,
                            'fecha_facturacion' => Carbon::parse($venta->FechaFactura)->format("Ymd"),
//                            'tipo_documento' => $venta->TipoDocumento == 1 ? "FA" : "NC",
                            'tipo_documento' => "FA",
                            'num_documento' => $venta->NumeroFactura,
                            'doc_referencia' => $venta->NotaVenta,
                            'precio' => $venta->ValorFactura,
                            'nombre_local' => $venta->sucursal->Sucursal ?? '',
                            'estado_envio' => 'N',
                            'rut_cliente' => $rut,
                            'nombre_cliente' => $venta->cliente->Nombre,
                            'direccion_cliente' => $venta->cliente->Direccion,
                            'ciudad_cliente' => 'SANTIAGO',
                            'telefono_cliente' => $venta->cliente->Telefono,
                            'rut_vendedor' => $rutVendedor,
                            'nombre_vendedor' => $venta->vendedor->Nombre,
                            'nv_referencia' => $venta->NotaVenta,
                            'rut_facturacion' => $rut,
                            'nombre_facturado' => $venta->cliente->Nombre,
//                            'id_facturacion_dybox' => 0,
                        ],
                    ]);

                    $xml = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $xml);

                    $req['data'] = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ind="http://www.indumotora.cl/">
                       <soapenv:Header/>
                       <soapenv:Body>
                          <ind:Publish>
                             <!--Optional:-->
                             <ind:id>001</ind:id>
                             <!--Optional:-->
                             <ind:canales>PompeyoCarrasco,venta</ind:canales>
                             <!-- toma el valor venta, ot , repuestos o meson  segun corresponda-->
                             <!--Optional:-->
                             <ind:msg>
                             <![CDATA[
                             ' . $xml . '
                                ]]>
                             </ind:msg>
                          </ind:Publish>
                       </soapenv:Body>
                    </soapenv:Envelope>';


                    $resp = $solicitudCon->store($req, 'aislado1');
                    echo("<br>" . ($resp->message ?? ''));

                }
            } else {
                Log::info("No se encontraron ventas");
            }

        } else {
            Log::error("Flujo no activo");
        }

        return true;
    }

    public function sendOTsSICIndumotora()
    {

        echo "Ejecutando Flujo KIA OT SIC<br>";
        Log::info("Inicio flujo OTs Indumotora");

        $flujo = FLU_Flujos::where('Nombre', 'KIA SIC')->first();

        if ($flujo->Activo) {
            Log::info("Flujo activo");
            $h = new FLU_Homologacion();

            $ordenes = PV_PostVenta::with('cliente')
                ->OrdenesKia()
                ->NoNotificado($flujo->ID)
                ->where('TipoOrigen', 'REAL')
//                ->where('FechaFacturacion', Carbon::now()->subDay()->format("Y-m-d"))
                ->where('FechaFacturacion', '>=', "2024-03-01 00:00:00")
                ->where('CategoriaOT', '<>', 'MESÓN')
//                ->limit($flujo->MaxLote ?? 5)
                ->get();

            if ($ordenes) {
                Log::info("Existen Ots");
                $solicitudCon = new ApiSolicitudController();
                Log::info("Cantidad de Ots : " . count($ordenes));

                foreach ($ordenes as $orden) {
                    print PHP_EOL . "Procesando orden : " . $orden->ID . PHP_EOL;
                    Log::info("Procesando orden : " . $orden->ID);
                    $req = new Request();
                    $req['referencia_id'] = $orden->ID;
                    $req['proveedor_id'] = 9;
                    $req['api_id'] = 27;
                    $req['prioridad'] = 1;
                    $req['flujoID'] = $flujo->ID;

                    $categoriaOT = $orden->CategoriaOT;

                    $checks = [
                        'ACCESORIOS POST VENTAS' => 'accesorios',
                        'ACCESORIOS PRE-VENTA' => 'accesorios',
                        'ACCESORIOS VENTAS' => 'accesorios',
                        'CAMPAÑA' => 'garantia',
                        'CIA. SEGUROS' => 'dyp',
                        'GARANTIA EXTENDIDA USADOS' => 'rep_varias',
                        'GARANTIA FABRICA' => 'garantia',
                        'MANTENCION' => 'mantencion',
                        'MECANICA GENERAL' => 'rep_varias',
                        'MESÓN' => 'meson',
                        'PARTICULAR DYP' => 'dyp',
                        'PROMOCIONES VENTAS VN' => 'rep_varias',
                        'REVISION VU x VU' => 'rep_varias',
                        'REVISIONES PRE-COMPRA' => 'rep_varias',
                        'SIN REGISTRO' => 'rep_varias',
                        'USADOS PRE-VENTA' => 'rep_varias',
                    ];

                    // Revision de RUT, validacion y creacion en Clientes
                    $rut = $orden->ClienteRut;
                    Log::info("Buscando cliente " . $rut);

                    // SI no trae - , significa que el rut no tiene digito y hay que calcular
                    if (str_contains($rut, '-') === false) {
                        $s = 1;
                        for ($m = 0; $rut != 0; $rut /= 10)
                            $s = ($s + $rut % 10 * (9 - $m++ % 6)) % 11;
                        $dv = chr($s ? $s + 47 : 75);
                        $rutCliente = $orden->ClienteRut . $dv;
                    } else {
                        $rutCliente = str_replace('-', '', $rut);
                    }

                    $cliente = MA_Clientes::where('Rut', str_replace('-', '', $rutCliente))->first();
                    if ($cliente) Log::info("Cliente encontrado " . $cliente->Nombre);
                    else Log::info("Cliente no encontrado");

                    // ----------------------------

                    $checkOtInterna = $categoriaOT == 'Factura Interna' ? 'X' : '';

                    $xml = XmlWriter::make()->write('exportacion', [
                        'ot' => [
                            'codigo_dealers' => 6, // Valor fijo (pompeyo)
                            'numero_ot' => $orden->FolioOT, // Codigo para KIA (externo)
                            'marca' => 1,
                            'fecha_atencion' => Carbon::parse($orden->FechaOT)->format("Ymd"),
                            'rut_recepcionista' => $h->getDato($orden->Recepcionista, $flujo->ID, 'asesor', 0),
                            'nombre_recepcionista' => $orden->Recepcionista,
                            'rut_mecanico' => '',
                            'nombre_mecanico' => $orden->NombreMecanico,
                            'seguro_automotriz' => '',
                            'vin' => $orden->Vin,
                            'numero_motor' => $orden->Chasis,
                            'numero_chasis' => $orden->Chasis,
                            'patente' => $orden->Patente,
                            'kilometraje' => $orden->Kilometraje,
                            'rut_cliente' => $orden->ClienteRut,
                            'tipo_persona' => '',
                            'razon_social' => '',
                            'nombres_cliente' => $orden->ClienteNombre,
                            'apellidos_cliente' => '',
                            'sexo' => '',
                            'fecha_nacimiento' => $cliente ? Carbon::parse($cliente->FechaNacimiento)->format("Ymd") : '',
                            'direccion' => $orden->ClienteDireccion,
                            'villa_poblacion' => '',
                            'codigo_region' => 13,
                            'nombre_region' => 'REGION METROPOLITANA',
                            'codigo_comuna' => $cliente->ComunaID ?? '',
                            'nombre_comuna' => $cliente->comuna->Comuna ?? '',
                            'telefono_comercial' => 0,
                            'telefono_particular' => $cliente->Telefono ?? '',
                            'telefono_movil' => 0,
                            'telefono_contacto' => 0,
                            'tipo_contacto' => 1,
                            'nombres_contacto' => '',
                            'apellido_contactos' => '',
                            'correo_electronico' => $cliente->Email ?? '',
                            'estado_ot' => 'F',
                            'mano_obra' => $orden->VentaManoObra,
                            'mano_obra_pint_desab' => $orden->VentaCarroceria,
                            'repuestos_servicio' => $orden->VentaRepuestos,
                            'repuestos_plaza' => 0,
                            'repuestos_colision' => 0,
                            'lubricantes_grasas' => $orden->VentaLubricantes,
                            'trabajos_terceros' => $orden->VentaServicioTerceros,
                            'materiales' => 0,
                            'descuentos' => $orden->Dctos,
                            'horas_vendidas' => 0,
                            'estado_envio' => '',
                            'sucursal' => $orden->Sucursal,
                            'kilometraje_mantencion' => $orden->Kilometraje,
                            'fecha_entrega' => Carbon::parse($orden->FechaFacturacion)->format("Ymd"),
                            'fecha_facturacion' => Carbon::parse($orden->FechaFacturacion)->format("Ymd"),
                            'mantencion' => (($checks[$categoriaOT] ?? '') == 'mantencion') ? 'X' : '',
                            'garantia' => (($checks[$categoriaOT] ?? '') == 'garantia') ? 'X' : '',
                            'dyp' => (($checks[$categoriaOT] ?? '') == 'dyp') ? 'X' : '',
                            'rep_varias' => (($checks[$categoriaOT] ?? '') == 'rep_varias') ? 'X' : '',
                            'accesorios' => (($checks[$categoriaOT] ?? '') == 'accesorios') ? 'X' : '',
                            'ot_interna' => $checkOtInterna,
                            'id_Facturacion_dybox' => '',
                            'numero_factura' => $orden->Folio,
                            'rut_facturado' => $orden->ClienteRutPagador,
                            'nombre_facturado' => $orden->ClienteNombrePagador,
                            'glosa_ot' => $orden->FolioOT,
                        ],
                    ]);

                    $xml = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $xml);
                    $req['data'] = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ind="http://www.indumotora.cl/">
                       <soapenv:Header/>
                       <soapenv:Body>
                          <ind:Publish>
                             <!--Optional:-->
                             <ind:id>001</ind:id>
                             <!--Optional:-->
                             <ind:canales>PompeyoCarrasco,ot</ind:canales>
                             <!-- toma el valor venta, ot , repuestos o meson  segun corresponda-->
                             <!--Optional:-->
                             <ind:msg>
                             <![CDATA[
                             ' . $xml . '
                                ]]>
                             </ind:msg>
                          </ind:Publish>
                       </soapenv:Body>
                    </soapenv:Envelope>';


                    $resp = $solicitudCon->store($req, 'aislado2');
                    echo("<br>" . ($resp->message ?? ''));
                }
            } else {
                Log::info("No se encontraron ventas");
            }

        } else {
            Log::error("Flujo no activo");
        }

        return true;
    }

    public function sendLeadMG()
    {
        echo "Ejecutando Flujo MG <br>";
        Log::info("Inicio de flujo MG Leads");

        $flujo = FLU_Flujos::where('Nombre', 'MG')->first();

        if ($flujo->Activo) {
            $h = new FLU_Homologacion();

            $dealerCode = [
                210 => 'SACL12043', // MG Independencia
                221 => 'SACL12053', // MG Melipilla
                195 => 'SACL12018', // MG Movicenter
                194 => 'SACL12019', // MG Irarrazabal
                202 => 'SACL12020', // MG Quilin
            ];

            $modelCode = [
                164 => 'MG3',
                165 => 'MG5',
                166 => 'MG6',
                167 => 'MG ZS',
                168 => 'MG ZX',
                169 => 'MG RX5',
                170 => 'MG HS',
                171 => 'MG ZX EV',
                182 => 'MG GT',
                1495 => 'MG ONE'
            ];

            $versionCode = [
                1909 => 'MG 3 1.5L AT COM',
                1910 => 'MG 3 1.5L AT STD',
                1911 => 'MG 3 1.5L MT COM',
                1912 => 'MG 3 1.5L MT STD',
                1915 => 'MG 5 1.5L CVT COM',
                1916 => 'MG 5 1.5L CVT DLX',
                1914 => 'MG 5 1.5L MT COM',
                4354 => 'MG 5 1.5L MT DLX',
                1913 => 'MG 5 1.5L MT STD',
                1919 => 'MG 6 1.5T DCT DLX',
                1920 => 'MG 6 1.5T DCT Trophy',
                1917 => 'MG 6 1.5T MT COM',
                1918 => 'MG 6 1.5T MT DLX',
                1951 => 'MG GT 1.5L CVT COM',
                1950 => 'MG GT 1.5L MT COM',
                4407 => 'MG GT 1.5T DCT DLX',
                1934 => 'MG HS 1.5T DCT DLX',
                1932 => 'MG HS 1.5T MT COM',
                1933 => 'MG HS 1.5T MT DLX',
                4402 => 'MG HS 2.0T DCT Trophy',
                1929 => 'MG RX5 1.5T DCT DLX',
                1930 => 'MG RX5 1.5T MT DLX',
                1931 => 'MG RX5 1.5T MT STD',
                4398 => 'MG RX5 2.0T DCT DLX AWD',
                1923 => 'MG ZS 1.5L AT COM',
                1922 => 'MG ZS 1.5L AT STD',
                1924 => 'MG ZS 1.5L MT COM',
                1921 => 'MG ZS 1.5L MT STD',
                4404 => 'MG ZS EV LUX',
                1928 => 'MG ZX 1.3T AT TROPHY',
                1926 => 'MG ZX 1.5L CVT DLX',
                1925 => 'MG ZX 1.5L MT COM',
                4395 => 'MG ZX 1.5L MT DLX',
                4408 => 'Marvel R DLX',
                4399 => 'MGZS 1.5 MT COM+ ',
                4406 => 'MG3 1.5L MT STD+ESP',
                4403 => 'MGZS 1.5 MT STD+',
                1936 => 'MGZS EV EDU DEL'
            ];

            $leadSource = 'Distributor Programmatic';
            $leadSourceCode = 'Distributor Programmatic';

            // obtener datos Leads MG (todas las fuentes)

            $solicitudCon = new ApiSolicitudController();

            $leads = MK_Leads::with('marca', 'modelo', 'version', 'estadoLead', 'cliente')
                ->porMarca($flujo->Nombre)
                ->Validado()
                ->NoNotificado($flujo->ID)
                ->Desde('2023-08-01')
                ->limit($flujo->MaxLote ?? 5)
                ->get();

            if ($leads) {
                foreach ($leads as $lead) {
                    print PHP_EOL . "Procesando orden : " . $lead->ID . PHP_EOL;
                    Log::info("Procesando orden : " . $lead->ID);
                    $req = new Request();
                    $req['referencia_id'] = $lead->ID;
                    $req['proveedor_id'] = 5;
                    $req['api_id'] = 1;
                    $req['prioridad'] = 1;
                    $req['flujoID'] = $flujo->ID;
//            $req['OnDemand'] = true;

                    $rut = $lead->cliente->Rut;
                    $nombreComuna = ucwords(strtolower($lead->cliente->comuna->Comuna));
                    $comuna = $h->GetDato($nombreComuna, $flujo->ID, 'comuna', '13101');

                    $provincia = substr($comuna, 0, 3);
                    $region = ($lead->cliente->region->H_TannerID != 0) ? $lead->cliente->region->H_TannerID : "13";
                    $rut = substr($rut, 0, strlen($rut) - 1) . "-" . substr($rut, -1, 1);

                    $req["data"] = [
                        "infoList" => [
                            [
                                "id" => $lead->ID,
                                "person_id" => $rut, // sin puntos y con guion
                                "first_name" => substr($lead->cliente->Nombre, 0, 40),
                                "last_name" => ".",
                                "lead_type" => "Personal",
                                "company_name" => "",
                                "gender" => "", // blanco sin genero
                                "region" => $region, // misma codificacion que tanner
                                "province" => $provincia,
                                "district" => $comuna,
                                "dealer_name" => "Pompeyo Carrasco",
                                "dealer_code" => $dealerCode[$lead->SucursalID] ?? 'SACL12043', // codigo sucursal homologado
                                "mobile" => $lead->cliente->Telefono ?? '',
                                "email" => $lead->cliente->Email ?? '',
                                "lead_source" => $leadSource,
                                "lead_source_code" => $leadSourceCode,
                                "campaign_name" => $lead->Campaign ?? '',
                                "interest_model" => ($lead->ModeloID != 1) ? ($modelCode[$lead->ModeloID] ?? "") : "",
                                "interest_version" => ($lead->VersionID != 1) ? ($versionCode[$lead->VersionID] ?? "") : "",
                                "need_finance" => $lead->financiamiento ?? false,
                                "car_as_payment" => false,
                                "pay_by_cash" => false,
                                "description" => "",
                                "privacy_data_processing" => true,
                                "privacy_marketing" => false,
                                "privacy_third_party" => false,
                                "generate_date" => $lead->FechaCreacion,
                                "test_drive_request" => false,
                                "test_drive_date" => "",
                                "offer_request" => false,
                                "offer_date" => "",
                                "cookie_id" => "",
                                "data_source" => "Chile Website",
                                "preferred_contact_method" => "Email",
                                "rating_rescore" => "Warm"
                            ]
                        ]
                    ];

                    $resp = $solicitudCon->store($req);
                    echo("<br>" . ($resp->message ?? ''));
                }

                Log::info("Flujo OK");
                return "Flujo OK";

            } else {
                $msg = "Sin registros para procesar";
                echo $msg;
                Log::info($msg);
                return $msg;
            }
        }

        return true;
    }

    public function sendCotizacionMG()
    {
        echo "Ejecutando Flujo MG (Cotizaciones) <br>";
        Log::info("Inicio de flujo MG Cotizaciones");

        $flujo = FLU_Flujos::where('Nombre', 'MG')->first();

        if ($flujo->Activo) {
            $h = new FLU_Homologacion();

            $dealerCode = [
                210 => 'SACL12043', // MG Independencia
                221 => 'SACL12053', // MG Melipilla
                195 => 'SACL12018', // MG Movicenter
                194 => 'SACL12019', // MG Irarrazabal
                202 => 'SACL12020', // MG Quilin
            ];

            $modelCode = [
                164 => 'MG3',
                165 => 'MG5',
                166 => 'MG6',
                167 => 'MG ZS',
                168 => 'MG ZX',
                169 => 'MG RX5',
                170 => 'MG HS',
                171 => 'MG ZX EV',
                182 => 'MG GT',
                1495 => 'MG ONE'
            ];

            $versionCode = [
                1909 => 'MG 3 1.5L AT COM',
                1910 => 'MG 3 1.5L AT STD',
                1911 => 'MG 3 1.5L MT COM',
                1912 => 'MG 3 1.5L MT STD',
                1915 => 'MG 5 1.5L CVT COM',
                1916 => 'MG 5 1.5L CVT DLX',
                1914 => 'MG 5 1.5L MT COM',
                4354 => 'MG 5 1.5L MT DLX',
                1913 => 'MG 5 1.5L MT STD',
                1919 => 'MG 6 1.5T DCT DLX',
                1920 => 'MG 6 1.5T DCT Trophy',
                1917 => 'MG 6 1.5T MT COM',
                1918 => 'MG 6 1.5T MT DLX',
                1951 => 'MG GT 1.5L CVT COM',
                1950 => 'MG GT 1.5L MT COM',
                4407 => 'MG GT 1.5T DCT DLX',
                1934 => 'MG HS 1.5T DCT DLX',
                1932 => 'MG HS 1.5T MT COM',
                1933 => 'MG HS 1.5T MT DLX',
                4402 => 'MG HS 2.0T DCT Trophy',
                1929 => 'MG RX5 1.5T DCT DLX',
                1930 => 'MG RX5 1.5T MT DLX',
                1931 => 'MG RX5 1.5T MT STD',
                4398 => 'MG RX5 2.0T DCT DLX AWD',
                1923 => 'MG ZS 1.5L AT COM',
                1922 => 'MG ZS 1.5L AT STD',
                1924 => 'MG ZS 1.5L MT COM',
                1921 => 'MG ZS 1.5L MT STD',
                4404 => 'MG ZS EV LUX',
                1928 => 'MG ZX 1.3T AT TROPHY',
                1926 => 'MG ZX 1.5L CVT DLX',
                1925 => 'MG ZX 1.5L MT COM',
                4395 => 'MG ZX 1.5L MT DLX',
                4408 => 'Marvel R DLX',
                4399 => 'MGZS 1.5 MT COM+ ',
                4406 => 'MG3 1.5L MT STD+ESP',
                4403 => 'MGZS 1.5 MT STD+',
                1936 => 'MGZS EV EDU DEL'
            ];

            $leadSource = 'Walk In';
            $leadSourceCode = 'Walk In';

            // obtener datos Leads MG (todas las fuentes)

            $solicitudCon = new ApiSolicitudController();

            $cotizaciones = VT_Cotizaciones::with('marca', 'modelo', 'version', 'estado', 'cliente')
                ->porMarca($flujo->Nombre)
                ->Validado()
                ->NoNotificado($flujo->ID)
                ->Desde('2023-10-01')
                ->where('EstadoID', 1)
                ->limit($flujo->MaxLote ?? 5)
                ->get();

            if ($cotizaciones) {
                foreach ($cotizaciones as $lead) {
                    print PHP_EOL . "Procesando orden : " . $lead->ID . PHP_EOL;
                    Log::info("Procesando orden : " . $lead->ID);
                    $req = new Request();
                    $req['referencia_id'] = $lead->ID;
                    $req['proveedor_id'] = 5;
                    $req['api_id'] = 1;
                    $req['prioridad'] = 1;
                    $req['flujoID'] = $flujo->ID;
//            $req['OnDemand'] = true;

                    $rut = $lead->cliente->Rut;
                    $nombreComuna = ucwords(strtolower($lead->cliente->comuna->Comuna));
                    $comuna = $h->GetDato($nombreComuna, $flujo->ID, 'comuna', '13101');

                    $provincia = substr($comuna, 0, 3);
                    $region = ($lead->cliente->region->H_TannerID != 0) ? $lead->cliente->region->H_TannerID : "13";
                    $rut = substr($rut, 0, strlen($rut) - 1) . "-" . substr($rut, -1, 1);

                    $req["data"] = [
                        "infoList" => [
                            [
                                "id" => $lead->ID,
                                "person_id" => $rut, // sin puntos y con guion
                                "first_name" => substr($lead->cliente->Nombre, 0, 40),
                                "last_name" => ".",
                                "lead_type" => "Personal",
                                "company_name" => "",
                                "gender" => "", // blanco sin genero
                                "region" => $region, // misma codificacion que tanner
                                "province" => $provincia,
                                "district" => $comuna,
                                "dealer_name" => "Pompeyo Carrasco",
                                "dealer_code" => $dealerCode[$lead->SucursalID] ?? 'SACL12043', // codigo sucursal homologado
                                "mobile" => $lead->cliente->Telefono ?? '',
                                "email" => $lead->cliente->Email ?? '',
                                "lead_source" => $leadSource,
                                "lead_source_code" => $leadSourceCode,
                                "campaign_name" => '',
                                "interest_model" => ($lead->ModeloID != 1) ? ($modelCode[$lead->ModeloID] ?? "") : "",
                                "interest_version" => ($lead->VersionID != 1) ? ($versionCode[$lead->VersionID] ?? "") : "",
                                "need_finance" => false,
                                "car_as_payment" => false,
                                "pay_by_cash" => false,
                                "description" => "",
                                "privacy_data_processing" => true,
                                "privacy_marketing" => false,
                                "privacy_third_party" => false,
                                "generate_date" => $lead->FechaCreacion,
                                "test_drive_request" => false,
                                "test_drive_date" => "",
                                "offer_request" => false,
                                "offer_date" => "",
                                "cookie_id" => "",
                                "data_source" => "Chile Website",
                                "preferred_contact_method" => "Email",
                                "rating_rescore" => "Warm"
                            ]
                        ]
                    ];

                    $resp = $solicitudCon->store($req);
                    echo("<br>" . ($resp->message ?? ''));
                }

                Log::info("Flujo OK");
                return "Flujo OK";

            } else {
                $msg = "Sin registros para procesar";
                echo $msg;
                Log::info($msg);
                return $msg;
            }
        }

        return true;
    }

    public function leadsGema()
    {
        echo "Ejecutando Flujo Gema <br>";
        Log::info("Inicio de flujo Gema");

        $flujo = FLU_Flujos::where('Nombre', 'GEMA')->first();

        if ($flujo->Activo) {
            $h = new FLU_Homologacion();

            echo ". . . <br>";

            $solicitudCon = new ApiSolicitudController();

            $referencia = $flujo->ID . date("ymdh");

            $req = new Request();
            $req['referencia_id'] = $referencia;
            $req['proveedor_id'] = 10;
            $req['api_id'] = 13;
            $req['prioridad'] = 1;
            $req['flujoID'] = $flujo->ID;
            $req['OnDemand'] = true;

            $req['data'] = "fecha_desde=" . date("Y-m-d 00:00:00", strtotime("-1 day"));

            $resp = $solicitudCon->store($req);
            $resp = $resp->getData();

            $solicitud = ApiSolicitudes::where('id', $resp->id)->first();

            if (substr($solicitud->Respuesta, 0, 4) == 'file') {
                $nombre = substr($solicitud->Respuesta, 5, strlen($solicitud->Respuesta));
                Log::info("Archivo json leads generado " . $nombre);

                $arrayData = json_decode(Storage::get($nombre));
            } else {
                $arrayData = json_decode($solicitud->Respuesta);
            }

//            dd($arrayData);

            $registros = 0;
//            Log::info("Datos a procesar : " . count($arrayData["data"]));

            $leadObj = new LeadController();

            foreach ($arrayData->data as $data) {
                $procedenciaID = $data->procedencia_id ?? 0;
                $procedencia = $data->procedencia ?? '';
                $tipoContacto = $data->tipo_contacto ?? 0;
                $fechaInteraccion = $data->fecha_interaccion ?? '';
                $nombreCliente = $data->nombre_cliente ?? '';
                $apellidoCliente = $data->apellido_cliente ?? '';
                $telefonoCliente = $data->telefono_cliente ?? '';
                $correoCliente = $data->correo_cliente ?? '';
                $mensaje = $data->mensaje ?? '';
                $vehiculoID = $data->vehiculo_id ?? 0;
                $patente = $data->patente ?? '';
                $marca = $data->marca ?? '';
                $modelo = $data->modelo ?? '';
                $version = $data->version ?? '';
                $sucursalID = $data->sucursal_id ?? 0;
                $sucursal = $data->sucursal ?? '';
                $vendedor = $data->vendedor ?? '';
                $mensajeLeido = $data->mensaje_leido ?? '';

                $req['data'] = [
                    "usuarioID" => 2892, // INTEGRACION GEMA
                    "reglaVendedor" => false,
                    "reglaSucursal" => false,
                    "nombre" => $nombreCliente . " " . $apellidoCliente,
                    "email" => $correoCliente,
                    "telefono" => $telefonoCliente,
                    "lead" => [
                        "idFlujo" => $flujo->ID,
                        "origenID" => 8,
                        "subOrigenID" => 36,
                        "sucursal" => $sucursal,
                        "marca" => $marca,
                        "modelo" => $modelo,
                        "comentario" => "Version : " . $version . "Mensaje : " . $mensaje,
                    ]
                ];

                $leadObj->nuevoLead($req);

            }
            return true;
        }
        return false;
    }


    public function notaVentaAPC()
    {
        echo "Ejecutando Flujo Nota Venta APC <br>";
        Log::info("Inicio de flujo Nota Venta APC");

        $flujo = FLU_Flujos::where('Nombre', 'APC_NV')->first();

        if ($flujo->Activo) {
            $h = new FLU_Homologacion();
            $solicitudCon = new ApiSolicitudController();

            $ventas = VT_Ventas::NoNotificado($flujo->ID)
//                ->vendido()
                ->desde('2023-12-01')
                ->limit($flujo->MaxLote ?? 5)
                ->where('VT_Ventas.ID', 168587)
//            dd($ventas->toSql());
                ->get();

            foreach ($ventas as $venta) {
//                $referencia = $flujo->ID . $venta->ID;
                $referencia = $venta->ID;
                print("Resolviendo venta : " . $referencia . "<br>");

                $req = new Request();
                $req['referencia_id'] = $referencia;
                $req['api_id'] = 15;
                $req['proveedor_id'] = 11;
                $req['prioridad'] = 1;
                $req['flujoID'] = $flujo->ID;
//                $req['OnDemand'] = true;

//                $resp = $solicitudCon->store($req);
//                $resp = $resp->getData();

                // Tramites
                $detalleTramites = [];
                if ($venta->tramites) {
                    $tramites = $venta->tramites;
                    foreach ($tramites as $tramite) {
                        $detalleTramites[] = [
                            "id_tramite" => $h->getDato($tramite->TipoID, $flujo->ID, 'tramite', 1),
                            "Cantidad" => 1,
                            "Precio_UnitarioTramite" => $tramite->Valor,
                            "cargo" => "C"
                        ];
                    }
                }

                // PAGOS
                $detallePagos = [];
                $pagos = SIS_Solicitudes::with('datosTransferencias', 'datosTransferencias.banco')
                    ->where('ReferenciaID', $venta->ID)
                    ->where('TipoID', 17)
                    ->where('EstadoID', 2)
                    ->get();
                if ($pagos) {
                    foreach ($pagos as $pago) {
                        $banco = 0;
                        foreach ($pago->datosTransferencias as $dato) {
                            $banco = $dato->banco->Banco ?? null;
                            $numeroDoc = (strlen($dato->NumeroDeposito) > 9) ? substr($dato->NumeroDeposito, 0, 9) : $dato->NumeroDeposito;

                            $detallePagos[] = [
                                "id_Medio_Pago" => $h->getDato($dato->FormaPago, $flujo->ID, 'medio_pago', 1),
                                "id_Banco" => $h->getDato($banco, $flujo->ID, 'banco', 0),
                                "Numero_Documento" => $numeroDoc,
                                "Fecha_Documento" => Carbon::parse($dato->FechaDeposito)->format('d/m/Y'),
                                "Vencimiento_Documento" => Carbon::parse($dato->FechaDeposito)->format('d/m/Y'),
                                "Monto" => $dato->Monto
                            ];
                        }
                    }
                }

                // Datos RETOMA
                $detalleRetoma = [];
                if ($venta->vpp) {
                    foreach ($venta->vpp as $vpp) {
                        $detalleRetoma[] =
                            [
                                "Patente" => $vpp->Patente,
                                "Marca" => $h->getDato($vpp->MarcaID, $flujo->ID, 'marca', 1),
                                "Modelo" => $h->getDato($vpp->ModeloID, $flujo->ID, 'modelo', 1),
                                "anio" => $vpp->Anio,
                                "kilometraje" => $vpp->Kilometraje,
                                "Numero_Vin" => $vpp->vehiculo->Vin ?? '',
                                "Numero_Serie" => $vpp->vehiculo->Vin ?? '',
                                "Numero_Motor" => $vpp->vehiculo->Vin ?? '',
                                "Numero_Chasis" => $vpp->vehiculo->Vin ?? '',
                                "id_Color_exterior" => $h->getDato($vpp->vehiculo->ColorID, $flujo->ID, 'color', 0),
                                "Precio_Retoma" => $vpp->PrecioCompra,
                                "id_Color_interior" => 0,
                                "id_Condicion_Vehiculo" => 4,
                                "Digito_Placa_Pantente" => 0,
                                "Fecha_Inscripcion" => "",
                                "Fecha_Vencto_Revision_Tecnica" => "", // "01/01/1900",
                                "id_Estado_Placa_Patente" => 2,
                                "Cantidad_Propietarios" => 1,
                                "Garantia" => 0,
                                "Transferido" => 0,
                                "Numero_Llave" => "",
                                "Codigo_Radio" => ""
                            ];
                    }
                }

                // CLIENTE

                $rutCliente = substr($venta->cliente->Rut, 0, strlen($venta->cliente->Rut) - 1);
                $rutDvCliente = substr($venta->cliente->Rut, -1, 1);
                $nombreCliente = explode(" ", $venta->cliente->Nombre);

                $reqCliente = new Request();
                $reqCliente['referencia_id'] = $venta->cliente->Rut;
                $reqCliente['api_id'] = 25;
                $reqCliente['proveedor_id'] = 11;
                $reqCliente['prioridad'] = 1;
                $reqCliente['flujoID'] = $flujo->ID;
                $reqCliente['OnDemand'] = true;
                $reqCliente["data"] = [
                    "IdTipoRegistroDTO" => 11,
                    "NombresDTO" => (count($nombreCliente) > 3) ? $nombreCliente[0] . ' ' . $nombreCliente[1] : $nombreCliente[0],
                    "ApellidoPaternoDTO" => (count($nombreCliente) > 3) ? $nombreCliente[2] : '',
                    "ApellidoMaternoDTO" => (count($nombreCliente) > 3) ? $nombreCliente[3] : '',
                    "VigenciaDTO" => 'true',
                    "EsExtranjeroDTO" => 'false',
                    "CodigoUnicoExtranjeroDTO" => '',
                    "RutDTO" => $rutCliente,
                    "DigitoDTO" => $rutDvCliente,
                    "IdSexoDTO" => 1,
                    "FechaNacimientoDTO" => $venta->cliente->FechaNacimiento,
                    "IdTipoClienteDTO" => 312,
                    "GiroComercialDTO" => 'Persona',
                    "DireccionDTO" => $venta->cliente->Direccion,
                    "IdComunaDTO" => $venta->cliente->ComunaID,
                    "CodigoPostalDTO" => '',
                    "TelefonoFijoDTO" => $venta->cliente->Telefono,
                    "TelefonoMovilDTO" => $venta->cliente->Telefono,
                    "EmailDTO" => $venta->cliente->Email,
                ];

                $resp = $solicitudCon->store($reqCliente)->getData();
                Log::info(print_r($resp, true));

                if (isset($resp->id)) {
                    $solicitud = ApiSolicitudes::where('id', $resp->id)->first();
                    if ($solicitud->CodigoRespuesta === 502) {
                        ApiSolicitudController::reprocesarJob($solicitud);
                    }
                }
                echo("<br>" . ($resp->message ?? ''));


                // Creacion de solicitud

                // datos

                $req["data"] = [
                    "Fecha" => Carbon::parse($venta->FechaVenta)->format('d/m/Y'),
                    "Empresa" => 205,
                    "Sucursal" => $h->getDato($venta->SucursalID, $flujo->ID, 'sucursal', 1),
                    "vendedor" => $venta->vendedor->RutFormat,
                    "Rut_Cliente_Documento" => $venta->cliente->RutFormat,
                    "Comprapara" => 0,
                    "Rut_Cliente_facturacion" => $venta->cliente->RutFormat,
                    "Id_Vehiculo" => trim($venta->Cajon),
                    "Fecha_Entrega" => Carbon::parse($venta->FechaActaEntrega)->format('d/m/Y'),
                    "Precio_Venta_Descuento" => 0, // $venta->DescuentoVendedor
                    "Precio_Venta_Descuento_Pje" => 0, // $venta->DescuentoVendedor
                    "DetalleTramites" => $detalleTramites,
                    "DetalleContratos" => [],
                    "DetallePagos" => $detallePagos,
                    "DetalleRetoma" => $detalleRetoma
                ];

                print_r(json_encode($req["data"], JSON_UNESCAPED_SLASHES));

                $resp = $solicitudCon->store($req);
                echo("<br>" . ($resp->message ?? ''));
            }

            Log::info("Flujo OK");
            return "Flujo OK";

        } else {
            Log::info("Flujo no activo");
            return "Flujo no activo";
        }

        return true;
    }

    public function sendLeadInchcape()
    {
        echo "Ejecutando Flujo Inchcape <br>";
        Log::info("Inicio de flujo Inchcape");

        $flujo = FLU_Flujos::where('Nombre', 'Inchcape')->first();

        if ($flujo->Activo) {
            $h = new FLU_Homologacion();

            $solicitudCon = new ApiSolicitudController();

            $leads = MK_Leads::with('marca', 'modelo', 'version', 'estadoLead', 'cliente')
                ->porMarca($flujo->Nombre)
                ->Validado()
                ->NoNotificado($flujo->ID)
                ->Desde('2023-08-01')
                ->limit($flujo->MaxLote ?? 5)
                ->get();

            if ($leads) {
                foreach ($leads as $lead) {
                    print PHP_EOL . "Procesando orden : " . $lead->ID . PHP_EOL;
                    Log::info("Procesando orden : " . $lead->ID);
                    $req = new Request();
                    $req['referencia_id'] = $lead->ID;
                    $req['proveedor_id'] = 5;
                    $req['api_id'] = 1;
                    $req['prioridad'] = 1;
                    $req['flujoID'] = $flujo->ID;
                }
            }
        }
    }


    public function leadsHubspot()
    {
        echo "Ejecutando Flujo Hubspot <br>";
        Log::info("Inicio de flujo Hubspot");

        $flujo = FLU_Flujos::where('Nombre', 'Leads Hubspot')->first();

        if ($flujo->Activo) {

            $token = json_decode($flujo->Opciones);
            $client = Factory::createWithAccessToken($token->token);

            $filter = new Filter();
            $filter->setOperator('NOT_HAS_PROPERTY')
                ->setPropertyName('idpompeyo');
            $filter2 = new Filter();
            $filter2->setOperator('EQ')
                ->setPropertyName('canal')
                ->setValue('web');

            $filterGroup = new \HubSpot\Client\Crm\Contacts\Model\FilterGroup();
            $filterGroup->setFilters([$filter, $filter2]);
//            $filterGroup->setFilters([$filter]);

            $searchRequest = new \HubSpot\Client\Crm\Contacts\Model\PublicObjectSearchRequest();
            $searchRequest->setFilterGroups([$filterGroup])
                ->setLimit($flujo->MaxLote)
                ->setAfter('0');

            $searchRequest->setProperties(['firstname,lastname,phone,email,rut, marca,modelo,hs_analytics_source_data_1,compra_con_financiamiento,reglasucursal,reglavendedor,canal,vpp,financiamiento,sucursal,idpompeyo,origen']);

            try {
                $apiResponse = $client->crm()->contacts()
                    ->searchApi()->doSearch($searchRequest)
                    ->getResults();

                Log::info("Leads a procesar : " . count($apiResponse));
                foreach ($apiResponse as $item) {
                    $data = $item->jsonSerialize();

                    print("Buscando Lead : " . $data->id . "<br>");
                    $lead = MK_Leads::where('IDExterno', $data->id)->first();

                    $newProperties = new \HubSpot\Client\Crm\Contacts\Model\SimplePublicObjectInput();

                    if ($lead) {
                        print("Lead encontrado : " . $lead->ID . "<br>");
                        Log::info("Lead encontrado : " . $lead->ID) . " - " . $lead->IDExterno;

                        $newProperties->setProperties([
                            'idpompeyo' => $lead->ID,
                            'idvendedor' => $lead->VendedorID,
                            'nombrevendedor' => $lead->vendedor->Nombre,
                        ]);
                        $client->crm()->contacts()->basicApi()->update($data->id, $newProperties);

                    } else {
                        print("Lead no encontrado <br>");
                        Log::info("Creando nuevo Lead");
                        $marca = $data->properties['marca'] ?? '';
                        $modelo = $data->properties['modelo'] ?? '';
                        $fuente = $data->properties['hs_analytics_source_data_1'] ?? '';
                        $nombre = $data->properties['firstname'] . ' ' . $data->properties['lastname'] ?? '';
                        $email = $data->properties['email'] ?? '';
                        $telefono = $data->properties['phone'] ?? '';
                        $origenProp = $data->properties['origen'] ?? '';
                        $idExterno = $data->id ?? '';

                        $vpp = $data->properties['vpp'] ?? 0;
                        if ($vpp === 'SI') {
                            $vpp = 1;
                        } else {
                            $vpp = 0;
                        }

                        $reglaSucursal = $data->properties['reglasucursal'] ?? 1;
                        $reglaVendedor = $data->properties['reglavendedor'] ?? 1;
                        $rut = $data->properties['rut'] ?? '';
                        $sucursal = $data->properties['sucursal'] ?? '';
                        $canal = $data->properties['canal'] ?? '';

                        $financiamiento = $data->properties['financiamiento'] ?? 0;
                        if ($financiamiento === 'SI') {
                            $financiamiento = 1;
                        } else {
                            $financiamiento = 0;
                        }

                        $comentario = ($vpp) ? ' *Tiene VPP ' : '';

                        if ($fuente == 'Facebook' || $origenProp == 'Facebook') {
                            $origen = 8;
                            $subOrigen = 36;
                        } else if ($origenProp == "Whatsapp") {
                            $origen = 3;
                            $subOrigen = 14;
                        } else {
                            $origen = 8;
                            $subOrigen = 36;
                        }
//                            $reglaVendedor = false;
//                            $reglaSucursal = false;


                        $leadObj = new LeadController();
                        $req = new Request();
                        $req['data'] = [
                            "usuarioID" => 2904, // INTEGRACION HUBSPOT
                            "reglaVendedor" => $reglaVendedor,
                            "reglaSucursal" => $reglaSucursal,
                            "rut" => $rut,
                            "nombre" => $nombre,
                            "email" => $email,
                            "telefono" => $telefono,
                            "lead" => [
                                "idFlujo" => $flujo->ID,
                                "origenID" => $origen,
                                "subOrigenID" => $subOrigen,
                                "sucursal" => $sucursal,
                                "marca" => $marca,
                                "modelo" => $modelo,
                                "comentario" => $comentario,
                                "externalID" => $idExterno,
                                "financiamiento" => $financiamiento,
                            ]
                        ];

                        $resultado = $leadObj->nuevoLead($req);
                        if ($resultado) {
                            $res = $resultado->getData();

                            print("Nuevo Lead ");
                            if ($res->LeadID > 0) {
                                $lead = MK_Leads::where('ID', $res->LeadID)->first();

                                $newProperties->setProperties([
                                    'idpompeyo' => $lead->ID,
                                    'idvendedor' => $lead->VendedorID,
                                    'nombrevendedor' => $lead->vendedor->Nombre,
                                ]);
                                $client->crm()->contacts()->basicApi()->update($data->id, $newProperties);
                            }

                        } else {
                            print("Error al crear Lead ");
                        }

                    }
                }

                Log::info("Flujo OK");
                return true;

            } catch (ApiException $e) {
                echo "Exception when calling basic_api->get_page: ", $e->getMessage();
                return false;
            }

        }
    }

    public function reprocesarSolicitudes()
    {
        $solicitudes = ApiSolicitudes::where('Reprocesa', 1)
            ->get();

        $logger = new Logger();

        if ($solicitudes) {

            foreach ($solicitudes as $solicitud) {
                $reintentos = $solicitud->Reintentos;
                $logger->info("Reprocesando solicitud : " . $solicitud->id . " - Reintentos : " . $reintentos);

                if ($reintentos) {
                    $solicitud->Reintentos = $reintentos - 1;
                    $solicitud->Reprocesa = 0;
                    $solicitud->save();

                    $tiempoEspera = $solicitud->flujo->TiempoEspera ?? 0;
                    $fechaResolucion = Carbon::parse($solicitud->FechaResolucion)->addSeconds($tiempoEspera);
                    if ($fechaResolucion < Carbon::now()) {
                        ApiSolicitudController::reprocesarJob($solicitud);
                    } else {
                        $logger->info("Solicitud : " . $solicitud->id . " - Esperando fecha de resolucion");
                    }
                    $logger->solveArray($solicitud->id);

                } else {
                    $solicitud->Reprocesa = 0;
                    $solicitud->save();
                }

            }
        } else {
            Log::info("No hay solicitudes para reprocesar");
        }
    }


    public function sendCpdVentas()
    {
        echo "Ejecutando Flujo CPD Ventas <br>";
        Log::info("Inicio de flujo CPD Ventas");

        $flujo = FLU_Flujos::where('Nombre', 'Venta CPD')->first();

        if ($flujo->Activo) {

            $solicitudCon = new ApiSolicitudController();

            $ventas = VT_Ventas::NoNotificado($flujo->ID)
                ->vendido()
                ->where('TieneVPP', 1)
                ->where('FechaActaEntrega', '>=', date("Y-m-d H:i:s", strtotime("-30 day")))
//                ->desde('2023-12-01')
                ->limit($flujo->MaxLote ?? 5)
                ->get();

            if ($ventas) {
                foreach ($ventas as $venta) {
                    $referencia = $flujo->ID . $venta->ID;
                    print("Resolviendo venta : " . $referencia . "<br>");

                    $req = new Request();
                    $req['referencia_id'] = $referencia;
                    $req['api_id'] = 24;
                    $req['proveedor_id'] = 6;
                    $req['prioridad'] = 1;
                    $req['flujoID'] = $flujo->ID;

                    $req['data'] = [
                        "idVenta" => $venta->ID,
                    ];


                    $resp = $solicitudCon->store($req);
                    echo("<br>" . ($resp->message ?? ''));

                }
            } else {
                Log::info("No se encontraron ventas");
            }

        } else {
            Log::error("Flujo no activo");
        }

        return true;
    }

    public function autoredTransactions()
    {
        echo "Ejecutando Flujo Autored Transactions between dates <br>";
        Log::info("Inicio de flujo Hubspot");

        $flujo = FLU_Flujos::where('Nombre', 'Autored transactions')->first();


        if ($flujo->Activo) {

            $solicitudCon = new ApiSolicitudController();

            $referencia = $flujo->ID . date("ymdh");

            $req = new Request();
            $req['referencia_id'] = $referencia;
            $req['api_id'] = 26;
            $req['proveedor_id'] = 4;
            $req['prioridad'] = 1;
            $req['flujoID'] = $flujo->ID;
            $req['OnDemand'] = true;

            $req['data'] = [
//                "from" => Carbon::now()->subDays(1)->format('Y-m-d'),
                "from" => "2024-01-01",
//                "to" => Carbon::now()->format('Y-m-d'),
                "to" => "2024-01-31",
            ];


            $resp = $solicitudCon->store($req);
            $resp = $resp->getData();

            $solicitud = ApiSolicitudes::where('id', $resp->id)->first();

            if (substr($solicitud->Respuesta, 0, 4) == 'file') {
                $nombre = substr($solicitud->Respuesta, 5, strlen($solicitud->Respuesta));
                Log::info("Archivo json stock generado " . $nombre);

                $arrayData = json_decode(Storage::get($nombre));
            } else {
                $arrayData = json_decode($solicitud->Respuesta);
            }


            $registros = 0;
            if ($arrayData) {
                Log::info("Datos a procesar : " . count($arrayData));

                foreach ($arrayData as $data) {
                    $fechaCreacion = Carbon::createFromFormat("d/m/Y H:i", $data->created_at)->format('Y-m-d H:i:s');

                    $fechaRecepcion = $fechaCreacion;

                    $fechaInspeccion = Carbon::parse($data->inspection_request)->format('Y-m-d H:i:s');

                    $sucursalID = FLU_Homologacion::getDato($data->branch_name, $flujo->ID, 'sucursal', 1);

                    $vendedorID = MA_Usuarios::where('Email', $data->seller_email)->first();
                    $vendedorID = $vendedorID ? $vendedorID->ID : null;

                    $registro = [
                        'ID' => 0,
                        'FechaCreacion' => $fechaCreacion,
                        'Patente' => $data->vehicle->license_plate,
                        'Marca' => $data->vehicle->brand,
                        'Modelo' => $data->vehicle->model,
                        'Ano' => $data->vehicle->year,
                        'Km' => $data->vehicle->km,
                        'Version' => $data->vehicle->version,
                        'Color' => $data->vehicle->color,
                        'Sucursal' => $data->branch_name,
                        'Vendedor' => $data->seller_name . ' ' . $data->seller_surname,
                        'EmailVendedor' => $data->seller_email,
                        'CodigoVendedor' => $data->seller_id,
                        'Creador' => $data->seller_name . ' ' . $data->seller_surname,
                        'Estado' => $data->status,
                        'MotivoRechazo' => $data->reject_reason,
                        'DetalleRechazo' => $data->reject_comment,
                        'PrecioOferta' => $data->offers[0]->price ?? 0,
                        'AutorPrecio' => $data->offers[0]->price ?? 0,
                        'AtendidaPor' => ($data->offers[0]->name ?? '') . ' ' . ($data->offers[0]->surname ?? ''),
                        'PrecioSugerido' => $data->suggestions[0]->price ?? 0,
                        'PrecioPublicacion' => $data->publication_price,
                        'PrecioVenta' => $data->sale_price,
                        'NombreCliente' => $data->vehicle->client->name . ' ' . $data->vehicle->client->surname,
                        'RutCliente' => $data->vehicle->client->rut,
                        'EmailCliente' => $data->vehicle->client->mail,
                        'TelefonoCliente' => $data->vehicle->client->phone,
                        'CelularCliente' => $data->vehicle->client->mobile,
                        'TelefonoOficinaCliente' => $data->vehicle->client->office_phone,
                        'MarcaCliente' => $data->vehicle->client->brand,
                        'ModeloCliente' => $data->vehicle->client->model,
                        'FinanciamientoCliente' => $data->vehicle->client->funding ? 'Con financiamiento' : 'Sin financiamiento',
                        'ComentarioCliente' => '', //$data->vehicle->request_letter_comments,
                        'IDtransaccion' => $data->id,
                        'Origen' => 'sucursales',
                        'TipoCompra' => $data->vehicle->client->purchase_type_name,
                        'Procedencia' => $data->vehicle->client->origin_name,
                        'VehiculoRecibido' => $data->received_date ? 'Si' : 'No',
                        'FechaRecepcion' => $fechaRecepcion,
                        'Inspeccion' => $data->inspection_request ? 'Si' : 'No',
                        'FechaInspeccion' => $fechaInspeccion,
                        'IDAutoRed' => $data->id,

                        'SucursalID' => $sucursalID,
                        'VendedorID' => $vendedorID
                    ];

                    $transaccion = SIS_AutoRedTransaccion::updateOrCreate(
                        ['IDtransaccion' => $data->id],
                        $registro);
                }
            }

        }
    }

    public function autoredInspections()
    {
        echo "Ejecutando Flujo Autored Inspections between dates <br>";
        Log::info("Inicio de flujo Hubspot");

        $flujo = FLU_Flujos::where('Nombre', 'Autored transactions')->first();


        if ($flujo->Activo) {

            $solicitudCon = new ApiSolicitudController();

            $referencia = $flujo->ID . date("ymdh");

            $req = new Request();
            $req['referencia_id'] = $referencia;
            $req['api_id'] = 26;
            $req['proveedor_id'] = 4;
            $req['prioridad'] = 1;
            $req['flujoID'] = $flujo->ID;
            $req['OnDemand'] = true;

            $req['data'] = [
//                "from" => Carbon::now()->subDays(1)->format('Y-m-d'),
                "from" => "2024-01-01",
//                "to" => Carbon::now()->format('Y-m-d'),
                "to" => "2024-01-31",
            ];


            $resp = $solicitudCon->store($req);
            $resp = $resp->getData();

            $solicitud = ApiSolicitudes::where('id', $resp->id)->first();

            if (substr($solicitud->Respuesta, 0, 4) == 'file') {
                $nombre = substr($solicitud->Respuesta, 5, strlen($solicitud->Respuesta));
                Log::info("Archivo json stock generado " . $nombre);

                $arrayData = json_decode(Storage::get($nombre));
            } else {
                $arrayData = json_decode($solicitud->Respuesta);
            }


            $registros = 0;
            if ($arrayData) {
                Log::info("Datos a procesar : " . count($arrayData));

                foreach ($arrayData as $data) {
                    $fechaCreacion = Carbon::createFromFormat("d/m/Y H:i", $data->created_at)->format('Y-m-d H:i:s');

                    $fechaRecepcion = $fechaCreacion;

                    $fechaInspeccion = Carbon::parse($data->inspection_request)->format('Y-m-d H:i:s');

                    $sucursalID = FLU_Homologacion::getDato($data->branch_name, $flujo->ID, 'sucursal', 1);

                    $vendedorID = MA_Usuarios::where('Email', $data->seller_email)->first();
                    $vendedorID = $vendedorID ? $vendedorID->ID : null;

                    $registro = [
                        'ID' => 0,
                        'FechaCreacion' => $fechaCreacion,
                        'Patente' => $data->vehicle->license_plate,
                        'Marca' => $data->vehicle->brand,
                        'Modelo' => $data->vehicle->model,
                        'Ano' => $data->vehicle->year,
                        'Km' => $data->vehicle->km,
                        'Version' => $data->vehicle->version,
                        'Color' => $data->vehicle->color,
                        'Sucursal' => $data->branch_name,
                        'Vendedor' => $data->seller_name . ' ' . $data->seller_surname,
                        'EmailVendedor' => $data->seller_email,
                        'CodigoVendedor' => $data->seller_id,
                        'Creador' => $data->seller_name . ' ' . $data->seller_surname,
                        'Estado' => $data->status,
                        'MotivoRechazo' => $data->reject_reason,
                        'DetalleRechazo' => $data->reject_comment,
                        'PrecioOferta' => $data->offers[0]->price ?? 0,
                        'AutorPrecio' => $data->offers[0]->price ?? 0,
                        'AtendidaPor' => ($data->offers[0]->name ?? '') . ' ' . ($data->offers[0]->surname ?? ''),
                        'PrecioSugerido' => $data->suggestions[0]->price ?? 0,
                        'PrecioPublicacion' => $data->publication_price,
                        'PrecioVenta' => $data->sale_price,
                        'NombreCliente' => $data->vehicle->client->name . ' ' . $data->vehicle->client->surname,
                        'RutCliente' => $data->vehicle->client->rut,
                        'EmailCliente' => $data->vehicle->client->mail,
                        'TelefonoCliente' => $data->vehicle->client->phone,
                        'CelularCliente' => $data->vehicle->client->mobile,
                        'TelefonoOficinaCliente' => $data->vehicle->client->office_phone,
                        'MarcaCliente' => $data->vehicle->client->brand,
                        'ModeloCliente' => $data->vehicle->client->model,
                        'FinanciamientoCliente' => $data->vehicle->client->funding ? 'Con financiamiento' : 'Sin financiamiento',
                        'ComentarioCliente' => '', //$data->vehicle->request_letter_comments,
                        'IDtransaccion' => $data->id,
                        'Origen' => 'sucursales',
                        'TipoCompra' => $data->vehicle->client->purchase_type_name,
                        'Procedencia' => $data->vehicle->client->origin_name,
                        'VehiculoRecibido' => $data->received_date ? 'Si' : 'No',
                        'FechaRecepcion' => $fechaRecepcion,
                        'Inspeccion' => $data->inspection_request ? 'Si' : 'No',
                        'FechaInspeccion' => $fechaInspeccion,
                        'IDAutoRed' => $data->id,

                        'SucursalID' => $sucursalID,
                        'VendedorID' => $vendedorID
                    ];

                    $transaccion = SIS_AutoRedTransaccion::updateOrCreate(
                        ['IDtransaccion' => $data->id],
                        $registro);
                }
            }

        }
    }

    public function cargaIndicadoresUF()
    {

        $flujo = FLU_Flujos::where('Nombre', 'Datos CMF Indicadores')->first();

        $solicitudCon = new ApiSolicitudController();
        $req = new Request();
        $req['referencia_id'] = $flujo->ID;
        $req['api_id'] = 28;
        $req['proveedor_id'] = 13;
        $req['prioridad'] = 1;
        $req['flujoID'] = $flujo->ID;
        $req['OnDemand'] = true;
        $req['data'] = [];
        $resp = $solicitudCon->store($req);
        $resp = $resp->getData();

        $solicitud = ApiSolicitudes::where('id', $resp->id)->first();

        $arrayData = json_decode($solicitud->Respuesta);

        if ($arrayData) {
            foreach ($arrayData->UFs as $data) {
                $fecha = $data->Fecha;
                $valor = $data->Valor;
                $indicador = MA_IndicadorMonetario::updateOrCreate(
                    ['FechaIndicador' => $fecha,
                        'Tipo' => 'UF'],
                    ['Monto' => $valor,
                        'FechaIndicador' => $fecha,
                        'Tipo' => 'UF',
                        'Fuente' => 'CMF'
                    ]
                );


            }
        }
    }

    public function cargaIndicadoresUTM()
    {

        $flujo = FLU_Flujos::where('Nombre', 'Datos CMF Indicadores')->first();

        $solicitudCon = new ApiSolicitudController();
        $req = new Request();
        $req['referencia_id'] = $flujo->ID;
        $req['api_id'] = 29;
        $req['proveedor_id'] = 13;
        $req['prioridad'] = 1;
        $req['flujoID'] = $flujo->ID;
        $req['OnDemand'] = true;
        $req['data'] = [];
        $resp = $solicitudCon->store($req);
        $resp = $resp->getData();

        $solicitud = ApiSolicitudes::where('id', $resp->id)->first();

        $arrayData = json_decode($solicitud->Respuesta);

        if ($arrayData) {
            foreach ($arrayData->UTMs as $data) {
                $fecha = $data->Fecha;
                $valor = str_replace(".", "", $data->Valor);
                $indicador = MA_IndicadorMonetario::updateOrCreate(
                    ['FechaIndicador' => $fecha,
                        'Tipo' => 'UTM'],
                    ['Monto' => $valor,
                        'FechaIndicador' => $fecha,
                        'Tipo' => 'UTM',
                        'Fuente' => 'CMF'
                    ]
                );


            }
        }
    }
}
