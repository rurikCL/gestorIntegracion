<?php

namespace App\Http\Controllers\Flujo;

use App\Http\Controllers\Api\ApiSolicitudController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Logger;
use App\Models\Api\ApiSolicitudes;
use App\Models\FLU\FLU_Flujos;
use App\Models\FLU\FLU_Homologacion;
use App\Models\Lead;
use App\Models\MA\MA_Clientes;
use App\Models\MA\MA_Usuarios;
use App\Models\MK\MK_Leads;
use App\Models\PV\PV_PostVenta;
use App\Models\VT\VT_EstadoResultado;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Saloon\XmlWrangler\XmlWriter;
use function Psl\Str\Byte\length;
use function Symfony\Component\Translation\t;


class FlujoInchcapeController extends Controller
{

    private $h; // homologacion
    private $flujo;
    private $log;
    private $solicitudCon;

    public function __construct()
    {
        $this->flujo = FLU_Flujos::where('Nombre', 'INCHCAPE LEADS')->first();
        if (!$this->flujo) {
            Log::error("Flujo INCHCAPE LEADS no encontrado");
            abort(404, "Flujo INCHCAPE LEADS no encontrado");
        }
        $this->h = new FLU_Homologacion();
        $this->h->setFlujo($this->flujo->ID);

        $this->log = new Logger("INCHCAPE");

        $this->solicitudCon = new ApiSolicitudController();

    }

    public function sincronizaLeads(Request $request)
    {
        try {
            $leads = MK_Leads::where('LogEstado', '1')
                ->whereIn('MarcaID', [5,8])
                ->get();
            foreach ($leads as $lead) {
                // Aquí puedes agregar la lógica para sincronizar cada lead
                // Por ejemplo, actualizar el estado o enviar a otro servicio
            }
            return response()->json(['message' => 'Leads sincronizados correctamente'], 200);
        } catch (\Exception $e) {
            $this->log::error('Error sincronizando leads: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }


    public function cambiaFase($leadId)
    {

        $this->log->info("Cambia Fase INCHCAPE: " . $leadId);

        try {
            $lead = MK_Leads::where('IDExterno', $leadId)
                ->whereIn('MarcaID', [5,8])
                ->orderBy('ID', 'desc')
                ->first();

            if ($lead) {

                if ($lead->EstadoID == 8) {
                    $this->log->info("Lead se encuentra rechazado, enviando rechazo");
                    //TODO enviar rechazo a Inchcape
                } else {
                    // Homologación de estados y subestados
                    $estadoHomologado = intval($this->h->getD('estado', $lead->EstadoID, 100000001));
                    $subEstadoHomologado = intval($this->h->getD('subestado', $lead->EstadoID, 100000007));
                    $sucursalHomologada = intval($this->h->getR('sucursal', $lead->SucursalID));

                    // CAMBIO DE FASE (Partial Update)
                    $req = new Request();
                    $req['referencia_id'] = $lead->ID;
                    $req['proveedor_id'] = 49;
                    $req['prioridad'] = 1;
                    $req['flujoID'] = $this->flujo->ID;
                    $req['onDemand'] = false; // se envia el cambio a la cola de procesos
//                    $req['parentRef'] = $leadId; // Referencia del lead


                    $resp = $this->solicitudCon->store($req);
                    $resp = $resp->getData();
//                dump($resp);

                    return response()->json(['status' => 'OK', 'message' => 'Fase actualizada correctamente'], 200);
                }
            }

            return response()->json(['status' => 'ERROR', 'error' => 'Lead no encontrado'], 404);


        } catch (\Exception $e) {
            Log::error('Error cambiando fase del lead: ' . $e->getMessage());
            return response()->json(['status' => 'ERROR', 'error' => 'Internal Server Error'], 500);
        }

    }


    public function crearOportunidad($data, MK_Leads $lead)
    {
        $log = new Logger("INCHCAPE");
        $log->info("Crear Oportunidad INCHCAPE");

        $flujo = FLU_Flujos::where('Nombre', 'INCHCAPE LEADS')->first();
        $h = new FLU_Homologacion();
        $h->setFlujo($flujo->ID);
        $solicitudCon = new ApiSolicitudController();

        try {
            $lead = MK_Leads::where('IDExterno', $request->input('leadId'))
                ->whereIn('MarcaID', [5,8])
                ->orderBy('ID', 'desc')
                ->first();

            if ($lead) {
                // Lógica para crear la oportunidad
                // ...
                $req = new Request();
                $req['referencia_id'] = $lead->ID;
                $req['proveedor_id'] = 48;
                $req['prioridad'] = 1;
                $req['flujoID'] = $flujo->ID;
                $req['onDemand'] = false; // se envia el cambio a la cola de procesos
//                    $req['parentRef'] = $leadId; // Referencia del lead


                $resp = $solicitudCon->store($req);
                $resp = $resp->getData();

                return response()->json(['status' => 'OK', 'message' => 'Oportunidad creada correctamente'], 200);
            }

            return response()->json(['status' => 'ERROR', 'error' => 'Lead no encontrado'], 404);

        } catch (\Exception $e) {
            Log::error('Error creando oportunidad: ' . $e->getMessage());
            return response()->json(['status' => 'ERROR', 'error' => 'Internal Server Error'], 500);
        }
    }


    public function sendVentasInchcape()
    {

        echo "Ejecutando Flujo Ventas Inchcape <br>";
        Log::info("Inicio flujo Ventas Inchcape");

        $flujo = FLU_Flujos::where('Nombre', 'Inchcape SIC')->first();

        if ($flujo->Activo) {
            Log::info("Flujo activo");
//            $h = new FLU_Homologacion();

            $ventas = VT_EstadoResultado::with("modelo", "version", "apcstock", "cliente", "vendedor", "sucursal", "venta")
                ->Gerencia([1, 5])
                ->NoNotificado($flujo->ID)
                ->where('FechaDocumento', '>=', '2024-09-01 00:00:00')
//                ->where('FechaDocumento', '>=', Carbon::now()->subYears(2)->format("Y-m-d 00:00:00"))
                ->limit($flujo->MaxLote ?? 5)
                ->get();

//            dd($ventas->toArray());

            if ($ventas) {
                Log::info("Existen ventas");
//                $cuenta = $ventas->count();
                $solicitudCon = new ApiSolicitudController();
                Log::info("Cantidad de ventas : " . count($ventas));

                foreach ($ventas as $venta) {
                    print PHP_EOL . "Procesando Venta : " . $venta->ID . PHP_EOL;
                    Log::info("Procesando Venta : " . $venta->ID);
                    $req = new Request();
                    $req['referencia_id'] = $venta->ID;
                    $req['proveedor_id'] = 14; // 14 en prod
                    $req['api_id'] = 31;
                    $req['prioridad'] = 1;
                    $req['flujoID'] = $flujo->ID;

                    $rut = substr($venta->cliente->Rut, 0, length($venta->cliente->Rut) - 1) . "-" . substr($venta->cliente->Rut, -1);

                    $rutVendedor = substr($venta->vendedor->Rut, 0, length($venta->vendedor->Rut) - 1) . "-" . substr($venta->vendedor->Rut, -1);

                    if ($venta->apcstock) {
                        $modelo = $venta->apcstock->Modelo;
                        $version = $venta->apcstock->Version;

                        $vin = $venta->apcstock->VIN ?? $venta->Vin;
                        $color = $venta->apcstock->ColorExterior ?? $venta->ColorReferencial;
                    } else {
                        $modelo = $venta->modelo->Modelo;
                        $version = $venta->version->Version;
                        $vin = $venta->Vin;
                        $color = $venta->ColorReferencial;
                    }

                    if ($venta->sucursal->GerenciaID == 5) {
                        $marca = 2;
                    } else if ($venta->sucursal->GerenciaID == 1) {
                        $marca = 9;
                    } else {
                        $marca = 6;
                    }

                    $precioFinal = $venta->PrecioLista - ($venta->BonoFinanciamiento + $venta->BonoFinAdicional + $venta->BonoCliente + $venta->BonoMarca + $venta->BonoFlotas + $venta->BonoMantencionIncluida) - $venta->DescuentoVendedor;
//                    dd($venta);
                    $xml = XmlWriter::make()->write('exportacion', [
                        'venta' => [
                            'idventa' => $venta->VentaID,
                            'idorigen' => 10251,
                            'codigo_dealers' => 6, // Valor fijo (pompeyo)
                            'marca' => $marca, // Si es gerencia 5 (Subaru), o 1 DFSK
                            'modelo' => $modelo,
                            'vin' => $vin,
                            'version' => $version,
                            'color' => $color,
                            'fecha_facturacion' => Carbon::parse($venta->FechaFactura)->format("Ymd"),
                            'nombre_local' => $venta->sucursal->Sucursal,
                            'precio' => $precioFinal,
                            'tipo_documento' => $venta->TipoDocumento == 1 ? "FA" : "NC",
                            'num_documento' => $venta->venta->NumeroFactura,
                            'doc_referencia' => $venta->NotaVenta,
                            'rut_cliente' => $rut,
                            'nombre_cliente' => $venta->cliente->Nombre,
                            'direccion_cliente' => $venta->cliente->Direccion,
                            'ciudad_cliente' => 'SANTIAGO',
                            'telefono_cliente' => $venta->cliente->Telefono,
                            'mail_cliente' => $venta->cliente->Email,
                            'rut_vendedor' => $rutVendedor,
                            'nombre_vendedor' => $venta->vendedor->Nombre,
                            'rut_facturado' => $rut,
                            'nombre_facturado' => $venta->cliente->Nombre,
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
//                    dd($req['data']);

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

    public function sendOTsinchcape()
    {

        echo "Ejecutando Flujo KIA OT SIC<br>";
        Log::info("Inicio flujo OTs Indumotora");

        $flujo = FLU_Flujos::where('Nombre', 'Inchcape')->first();

        if ($flujo->Activo) {
            Log::info("Flujo activo");
            $h = new FLU_Homologacion();

            $tiposOrden = [
//                'ACCESORIOS POST VENTA',
//                'ACCESORIOS POST VENTAS',
                'MANTENCION',
                'MECANICA GENERAL',
                'PARTICULAR DYP',
                'COMPAÑIA SEGURO',
            ];

            $ordenes = PV_PostVenta::with('cliente', 'apcstock')
                ->whereIn('Marca', ['DFSK', 'SUBARU'])
                ->NoNotificado($flujo->ID)
                ->where('TipoOrigen', 'REAL')
                ->where('FechaFacturacion', '>=', "2024-09-01 00:00:00")
                ->where('TipoDocumento', '<>', 'Factura Interna')
                ->where(function ($query) use ($tiposOrden) {
                    $query->whereIn('TipoOT', $tiposOrden)
                        ->orWhere(function ($query) {
                            $query->where('TipoOT', 'MECANICA GENERAL');
                        });
                })
                ->get();
//            dd(self::getEloquentSqlWithBindings($ordenes));
//            dd($ordenes->toArray());


            if ($ordenes) {
                Log::info("Existen Ots");
                $solicitudCon = new ApiSolicitudController();
                Log::info("Cantidad de Ots : " . count($ordenes));

                foreach ($ordenes as $orden) {
                    print PHP_EOL . "Procesando orden : " . $orden->ID . PHP_EOL;
                    Log::info("Procesando orden : " . $orden->ID);
                    $req = new Request();
                    $req['referencia_id'] = $orden->ID;
                    $req['proveedor_id'] = 14;
                    $req['api_id'] = 32;
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
                    if ($orden->Marca == "DFSK") {
                        $marca = 9;
                    } else if ($orden->Marca == "SUBARU") {
                        $marca = 2;
                    }

                    $comentario = $orden->Mantencion;
                    if ($orden->Mantencion == 'No Ingresado') {
                        $comentario = $orden->CategoriaOT;
                    }

                    $xml = XmlWriter::make()->write('exportacion', [
                        'ot' => [
                            'codigo_dealers' => 6, // Valor fijo (pompeyo)
                            'numero_ot' => $orden->FolioOT, // Codigo para KIA (externo)
                            'marca' => $marca,
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
                            'mantencion' => (($checks[$categoriaOT] ?? '') == 'mantencion') ? 'X' : '',
                            'garantia' => (($checks[$categoriaOT] ?? '') == 'garantia') ? 'X' : '',
                            'dyp' => (($checks[$categoriaOT] ?? '') == 'dyp') ? 'X' : '',
                            'rep_varias' => (($checks[$categoriaOT] ?? '') == 'rep_varias') ? 'X' : '',
                            'accesorios' => (($checks[$categoriaOT] ?? '') == 'accesorios') ? 'X' : '',
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
                            'ot_interna' => $checkOtInterna,
                            'id_Facturacion_dybox' => '',
                            'numero_factura' => $orden->Folio,
                            'rut_facturado' => $orden->ClienteRutPagador,
                            'nombre_facturado' => $orden->ClienteNombrePagador,
                            'glosa_ot' => $comentario,
                            'modelo' => $orden->apcstock->Modelo ?? '',
                            'version' => $orden->apcstock->Version ?? ''
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



}
