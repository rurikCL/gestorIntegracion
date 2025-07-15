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
use App\Models\MA\MA_Usuarios;
use App\Models\MK\MK_Leads;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use function Symfony\Component\Translation\t;


class FlujoKiaController extends Controller
{

    public function getLeads(Request $request)
    {
        try {
            $leads = MK_Leads::where('Estado', 'Activo')->get();
            return response()->json(['data' => $leads], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching leads: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function sincronizaLeads(Request $request)
    {
        try {
            $leads = MK_Leads::where('LogEstado', '1')
                ->where('MarcaID', 2)
                ->get();
            foreach ($leads as $lead) {
                // Aquí puedes agregar la lógica para sincronizar cada lead
                // Por ejemplo, actualizar el estado o enviar a otro servicio
            }
            return response()->json(['message' => 'Leads sincronizados correctamente'], 200);
        } catch (\Exception $e) {
            Log::error('Error sincronizando leads: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }


    public function cambiaFase($leadId)
    {

        Log::info("Cambia Fase KIA: " . $leadId);

        $flujo = FLU_Flujos::where('Nombre', 'KIA')->first();
        $h = new FLU_Homologacion();
        $h->setFlujo($flujo->ID);

        $solicitudCon = new ApiSolicitudController();

        try {
            $lead = MK_Leads::where('IDExterno', $leadId)
                ->where('MarcaID', 2)
                ->orderBy('ID', 'desc')
                ->first();

            if ($lead) {

                if ($lead->EstadoID == 8) {
                    $this->rechazaLead($lead->IDExternoSecundario);
                } else {
                    $estadoHomologado = intval($h->getD('estado', $lead->EstadoID, 100000001));
                    $subEstadoHomologado = intval($h->getD('subestado', $lead->EstadoID, 100000007));
                    $sucursalHomologada = intval($h->getR('sucursal', $lead->SucursalID));
                    $esJefe = false;

                    if ($lead->VendedorID) {
                        $rutVendedor = $lead->vendedor->Rut;
                        $rutVendedor = substr($rutVendedor, 0, strlen($rutVendedor) - 1) . '-' . substr($rutVendedor, -1); // Asegurarse de que el RUT tenga el formato correcto
                        $sucursalVendedor = $lead->vendedor->SucursalID;
                        $vendedorActivo = $this->revisaRutVendedor($rutVendedor, $sucursalVendedor, $leadId);

                        if ($vendedorActivo['status'] == 'Inactivo') {
                            // buscar Jefe de sucursal y asignar ese rut
                            $jefe = MA_Usuarios::where('SucursalID', $sucursalVendedor)
                                ->where('CargoID', 2) // Jefe de sucursal
                                ->where('PerfilID', 3)
                                ->first();
                            if ($jefe) {
                                $rutVendedor = substr($jefe->Rut, 0, strlen($jefe->Rut) - 1) . '-' . substr($jefe->Rut, -1);
                                $subEstadoHomologado = intval($h->getD('subestadojefe', $lead->EstadoID, 100000008));
                                $esJefe = true;
                            } else {
                                Log::error("No se encontró un jefe de sucursal para el vendedor con rut: " . $rutVendedor);
                            }
                        } else {
                            if ($vendedorActivo["cargo"] == 'JEFE DE LOCAL' || $vendedorActivo["cargo"] == 'JEFE DE MARCA') {
                                $subEstadoHomologado = intval($h->getD('subestadojefe', $lead->EstadoID, 100000008));
                                $esJefe = true;
                            } else {
                                $subEstadoHomologado = intval($h->getD('subestado', $lead->EstadoID, 100000007));
                            }
                        }
                    }


                    // CAMBIO DE FASE
                    $req = new Request();
                    $req['referencia_id'] = $lead->ID;
                    $req['proveedor_id'] = 9;
                    $req['prioridad'] = 1;
                    $req['flujoID'] = $flujo->ID;
                    $req['onDemand'] = true;
                    $req['parentRef'] = $leadId; // Referencia del lead

                    if (!$esJefe) {
                        $req['api_id'] = 41; // api asigna vendedor
                        $req['data'] = [
                            'IdOportunidad' => $lead->IDExterno,
                            'ValorNuevoEstado' => $estadoHomologado,
                            'ValorNuevoSubEstado' => $subEstadoHomologado,
                            'Vendedor' => $rutVendedor, // RUT del vendedor
                            'RutSession' => '1234567-8',
                            'concesionario' => $sucursalHomologada
                        ];

                    } else {
                        $req['api_id'] = 46; // api asigna jefe
                        $req['data'] = [
                            "datosEntrada" => [
                                'IdOportunidad' => $lead->IDExterno,
                                'ValorNuevoEstado' => $estadoHomologado,
                                'ValorNuevoSubEstado' => $subEstadoHomologado,
                                'Vendedor' => $rutVendedor, // RUT del vendedor
                                'RutSession' => '1234567-8',
                                'concesionario' => $sucursalHomologada
                            ]
                        ];
                    }
                    $resp = $solicitudCon->store($req);
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

    public function revisaRutVendedor($rut, $sucursalID = 1, $ref = null)
    {

        print("Revisando rut: " . $rut);

        $flujo = FLU_Flujos::where('Nombre', 'KIA')->first();
        $solicitudCon = new ApiSolicitudController();
        $h = new FLU_Homologacion();
        $h->setFlujo($flujo->ID);

        try {
            $req = new Request();
            $req['referencia_id'] = $rut;
            $req['proveedor_id'] = 9;
            $req['api_id'] = 42;
            $req['prioridad'] = 1;
            $req['flujoID'] = $flujo->ID;
            $req['onDemand'] = true;
            $req['parentRef'] = $ref;

            $req['data'] = [
                'rut' => $rut,
                'dealerId' => '63345c480d4fd017470c4efc',
                'sucursalExternalID' => $h->getR('sucursal', $sucursalID), // ID SUCURSAL HOMOLOGADO
            ];

            $resp = $solicitudCon->store($req);
            $resp = $resp->getData();
            $solicitud = ApiSolicitudes::where('id', $resp->id)->first();
            $respuesta = json_decode($solicitud->Respuesta);
            if ($respuesta) {
                if ($respuesta->active === true) {
                    Log::info("Vendedor activo: " . $rut);
                    return [
                        'status' => 'Activo',
                        'cargo' => $respuesta->position ?? null,
                    ];
                } else {
                    Log::info("Vendedor inactivo: " . $rut);
                    return [
                        'status' => 'Inactivo',
                        'cargo' => null,
                    ];
                }
            }

            return [
                'status' => 'Inactivo',
                'cargo' => null,
            ];

        } catch (\Exception $e) {
            echo "<br>Error: " . $e->getMessage();
            Log::error('Error fetching vendedor: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function getFases()
    {

        $flujo = FLU_Flujos::where('Nombre', 'KIA')->first();
        $solicitudCon = new ApiSolicitudController();

        try {
            $req = new Request();
            $req['referencia_id'] = 0;
            $req['proveedor_id'] = 9;
            $req['api_id'] = 42;
            $req['prioridad'] = 1;
            $req['flujoID'] = $flujo->ID;
            $req['onDemand'] = true;

            $req['data'] = [
                'rut' => '12345678-9', // RUT de ejemplo, puede ser cualquier valor
                'dealerId' => '63345c480d4fd017470c4efc',
            ];

            $resp = $solicitudCon->store($req);
            $resp = $resp->getData();
            $solicitud = ApiSolicitudes::where('id', $resp->id)->first();
            $respuesta = json_decode($solicitud->Respuesta);
            if ($respuesta) {
                return $respuesta['active'] ?? false;
            }

            return false;

        } catch (\Exception $e) {
            echo "<br>Error: " . $e->getMessage();
            Log::error('Error fetching vendedor: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function rechazaLead($idCotizacion)
    {
        $log = new Logger("KIA");
        $log->info("Rechazando Lead KIA: " . $idCotizacion);

        $flujo = FLU_Flujos::where('Nombre', 'KIA')->first();
        $h = new FLU_Homologacion();
        $h->setFlujo($flujo->ID);
        $solicitudCon = new ApiSolicitudController();

        try {
            $lead = MK_Leads::where('IDExternoSecundario', $idCotizacion)
                ->where('MarcaID', 2)
                ->first();

            if ($lead) {
                $solicitud =

                $req = new Request();
                $req['referencia_id'] = $lead->ID;
                $req['proveedor_id'] = 9;
                $req['api_id'] = 44;
                $req['prioridad'] = 1;
                $req['flujoID'] = $flujo->ID;
                $req['onDemand'] = true;

                $req['data'] = [
                    "QuoteId" => $idCotizacion,
                    "state" => 3,
                    "status" => 6
                ];

                $resp = $solicitudCon->store($req);
                $resp = $resp->getData();

                $log->info("Respuesta de rechazo: " . json_encode($resp));
                return response()->json(['status' => 'OK', 'message' => 'Lead rechazado correctamente'], 200);
            }

            $log->error("Lead no encontrado para ID Cotización: " . $idCotizacion);
            return response()->json(['status' => 'ERROR', 'error' => 'Lead no encontrado'], 404);

        } catch (\Exception $e) {
            $log->error('Error rechazando lead: ' . $e->getMessage());
            return response()->json(['status' => 'ERROR', 'error' => 'Internal Server Error'], 500);
        }
    }


    public function crearOportunidad($data, MK_Leads $lead)
    {
        $log = new Logger("KIA");

        $log->info("Enviando Oportunidad KIA: " . $lead->ID . " " . $lead->cliente->Nombre . " " . $lead->cliente->Rut);

        $flujo = FLU_Flujos::where('Nombre', 'KIA')->first();
        $h = new FLU_Homologacion();
        $h->setFlujo($flujo->ID);
        $solicitudCon = new ApiSolicitudController();

        try {

            $modelo = $h->getR('modelo', $lead->ModeloID);
            $sucursal = $h->getR('sucursal', $lead->SucursalID);
            $marca = $h->getR('marca', $lead->MarcaID, 101430);
            $origen = $h->getD('origen', $data['origenNombre'] ?? $lead->origen->Alias, 100000020);
            $comentario = $data['comentario'] ?? '';
            $linkInteres = $data['link_interes'] ?? '';
            $agenda = $data['agenda'] ?? '';
            $comentarioFinal = $comentario . " Link: " . $linkInteres . " Agenda: " . $agenda;

            // busca la versión del modelo activa ----
            $log->info("Buscando version del modelo: " . $lead->ModeloID . " - " . $modelo);

            $req = new Request();
            $req['referencia_id'] = $lead->ID; // ID externo del lead
            $req['proveedor_id'] = 9;
            $req['api_id'] = 45; // ID de la API para revisar version
            $req['prioridad'] = 1;
            $req['flujoID'] = $flujo->ID;
            $req['onDemand'] = true;
            $req['data'] = [
                'codeSAPModel' => $modelo,
                'showWeb' => true,
                'showStock' => true,
                'active' => true
            ];
            $resp = $solicitudCon->store($req);
            $resp = $resp->getData();
            $solicitudVersion = ApiSolicitudes::find($resp->id);
            $dataVersion = json_decode($solicitudVersion->Respuesta);
            if ($dataVersion->status !== 'OK') {
                $log->error('Error obteniendo versión del modelo: ' . $dataVersion->message);
                $idVersion = 1;
            } else {
                $log->info('Versión del modelo obtenida: ' . ($dataVersion->data->codeSAP ?? 1));
                $idVersion = $dataVersion->data->codeSAP ?? 1; // ID de la versión del modelo
            }

            // Crea la oportunidad -------
            $req = new Request();
            $req['referencia_id'] = $lead->ID; // ID externo del lead
            $req['proveedor_id'] = 9;
            $req['api_id'] = 39; // ID de la API para crear oportunidades
            $req['prioridad'] = 1;
            $req['flujoID'] = $flujo->ID;
            $req['onDemand'] = true;

            $req['data'] = [
                'FechaCreacion' => Carbon::now()->format('Y-m-d h:i:s'),
                'Marca' => $marca,
                'FormaPago' => 0,
                'Origen' => $origen,
//                'RutEjecutivo' => substr($lead->vendedor->Rut, 0, strlen($lead->vendedor->Rut) - 1) . '-' . substr($lead->vendedor->Rut, -1), // RUT del vendedor
                'RutEjecutivo' => '', // RUT del vendedor
                'Rut' => substr($lead->cliente->Rut, 0, strlen($lead->cliente->Rut) - 1) . '-' . substr($lead->cliente->Rut, -1), // Asegurarse de que el RUT tenga el formato correcto
                'Nombres' => $data['nombre'],
                'Apellidos' => $data['apellido'],
                'IdConcesionario' => $sucursal,
                'Concesionario' => '',
                'CodTelefonoParticular' => '',
                'TelefonoParticular' => '',
                'CodTelefonoCelular' => '',
                'TelefonoCelular' => $data['telefono'],
                'CodTelefonoEmpresa' => '',
                'Telefonoempresa' => '',
                'CorreoElectronico' => $data['email'],
                'Comentario' => $comentarioFinal,
                'RecibirCorreos' => 1,
                'Sexo' => '',
                'ContactoPreferente' => 'Whatsapp',
                'VPP' => [
                    'ConVPP' => $data['lead']['vpp'] ?? false,
                ],
                'VehiculoCotizado' => [
                    'IdMarca' => $marca,
                    'Marca' => '',
                    'IdModelo' => $modelo,
                    'Modelo' => '',
                    'IdVersion' => $idVersion,
                    'Version' => '',
                    'Descripcion' => null,
                    'Precio' => '0',
                    'Unidad' => 1
                ],
                'Finaciamiento' => [
                    'siNo' => $data['lead']['financiamiento'] ?? false,
                ]
            ];

            $resp = $solicitudCon->store($req);
            $resp = $resp->getData();
            $solicitud = ApiSolicitudes::find($resp->id);
            $dataResponse = json_decode($solicitud->Respuesta);

            $idExterno = $dataResponse->oportunidades[0]->opportunityId ?? 1;
            $idExternoSecundario = $dataResponse->oportunidades[0]->quoteId ?? 1;
            $log->info("Oportunidad creada con ID Externo: " . $idExterno . " y Quote ID: " . $idExternoSecundario);
            // Actualiza la referencia de la solicitud con el ID externo
            $solicitud->ReferenciaID = $idExterno;
            $solicitud->save();

            // Asigna la solicitud previa como hija de la nueva solicitud
            $solicitudVersion->idSolicitudPadre = $solicitud->id;
            $solicitudVersion->save();

            $log->solveArray($solicitud->id);
            return response()->json(['status' => 'OK', 'message' => 'Oportunidad creada correctamente', 'ID' => $idExterno, 'IDQuote' => $idExternoSecundario, 'idSolicitud'=>$solicitud->id], 200);

        } catch (\Exception $e) {
            $log->error('Error creando oportunidad: ' . $e->getMessage());
            return response()->json(['status' => 'ERROR', 'error' => 'Internal Server Error'], 500);
        }

    }


}
