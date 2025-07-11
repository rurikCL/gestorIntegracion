<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\ApiSolicitudes;
use App\Models\FLU\FLU_Flujos;
use App\Models\FLU\FLU_Homologacion;
use App\Models\FLU\FLU_Notificaciones;
use App\Models\VT\VT_Cotizaciones;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FinancierasController extends Controller
{

    public function enviarFinancieras(Request $request)
    {

        $cotizacionID = $request->input("data.CotizacionID");
        $cotizacion = VT_Cotizaciones::where('ID', $cotizacionID)->first();

        if ($request->input("data.Financiera") == "SANTANDER"
            || $request->input("data.Financiera") == "TODAS") {

            $data = [
                "CotizacionID" => $cotizacion->ID,
                "SucursalID" => $cotizacion->sucursal->ID,
                "MarcaID" => $cotizacion->marca->ID,
                "ModeloID" => $cotizacion->modelo->ID,
                "Cliente" => $cotizacion->cliente,
                "Anno" => $cotizacion->Anno,
                "Precio" => $cotizacion->ValorVehiculo,
                "Pie" => $cotizacion->Pie,
                "Plazo" => $cotizacion->CantidadCuotas,
                "Vencimiento" => $cotizacion->FechaVencimiento,
            ];

            $respuestaSimulacion = $this->simulacionSantander($data);

            if ($respuestaSimulacion["status"] == "OK") {
                $respuestaCredito = $this->creditoSantander($respuestaSimulacion["data"]);
                if ($respuestaCredito["status"] == "OK") {
                    return [
                        "status" => "OK",
                        "message" => "Simulacion y Credito creados con exito",
                        "data" => [
                            "simulacion" => $respuestaSimulacion["data"],
                            "credito" => $respuestaCredito["data"],
                        ]
                    ];
                } else {
                    return [
                        "status" => "ERROR",
                        "message" => "Error al crear Credito",
                        "data" => [
                            "simulacion" => $respuestaSimulacion["data"],
                            "credito" => $respuestaCredito["data"],
                        ]
                    ];
                }
            } else {
                return [
                    "status" => "ERROR",
                    "message" => "Error al crear Simulacion",
                    "data" => [
                        "simulacion" => $respuestaSimulacion["data"],
                        "credito" => [],
                    ]
                ];
            }
        }

        return [
            "status" => "ERROR",
            "message" => "Ocurrio un error"
        ];
    }

    public function calculadoraSantander(Request $request)
    {

        Log::info("Inicio Calculadora Santander");
        $data = $request->input("data");
        $gastos = [
            [
                "id" => 24,
                "nombre" => "Gastos de Constitución"
            ]
        ];

        $seguros = [];
        // Seguro aplica a clientes dependientes e independientes
        if ($data["tipoTrabajador"] == "dependiente") {
            $seguros = [
                [
                    "id" => 160,
                    "nombre" => "Seguro de Desgravamen"
                ]
            ];
        }


        $rut = $data["rut"];
        $dataPayload = [
            "c_rut" => substr($rut, 0, strlen($rut) - 1),
            "c_dv" => substr($rut, -1, 1),
            "c_tipo_id" => "D", // D: Dependiente, E: Empresa, I: Independiente
            "c_pais_id" => "CHL",
            "c_ingreso" => $data["sueldoLiquido"],
            "v_org_id" => "2251",
            "v_estado_id" => "N", // N: Nuevo, U: Usado, SN: Seminuevo, NA: No aplica
            "v_uso_id" => $data["tipoUso"], // C: Comercial, P: Particular, NA: NO aplica
            "v_marca_id" => $data["marcaID"],
            "v_modelo_id" => $data["modeloID"],
            "v_logica_modelo" => 3, // 1: Drive Nombre, 3: Drive ID
            "v_ano" => $data["annoVehiculo"],
            "v_precio" => $data["precioVehiculo"],
            "v_linea_id" => "N",
            "p_id" => 1,
            "p_pie" => $data["pie"],
            "p_plazo" => $data["plazo"],
            "p_vfmg" => 0,
            "p_fecha_primer_venc" => Carbon::create($data["primerVencimiento"])->format("d-m-Y"),
            "p_g" => $gastos,
            "p_s" => $seguros,
            "p_m" => null,
        ];


        $solicitudCon = new ApiSolicitudController();
        $flujo = FLU_Flujos::where('Nombre', 'SANTANDER')->first();

        $referencia = $data["referencia"];

        $req = new Request();
        $req['referencia_id'] = $referencia;
        $req['proveedor_id'] = 8;
        $req['api_id'] = 7;
        $req['prioridad'] = 1;
        $req['flujoID'] = $flujo->ID;
        $req['onDemand'] = true;
        $req['data'] = $dataPayload;

        $resp = $solicitudCon->store($req);

        $data = $solicitudCon->getData($resp);

        if ($data != null) {

            if (isset($data->errors)) {
                Log::error("Error al crear solicitud Santander: " . $data->errors[0]->message);
                return [
                    "status" => "ERROR",
                    "message" => $data->errors[0]->message,
                    "data" => []
                ];
            } else {
                FLU_Notificaciones::Notificar($referencia, $flujo->ID);
                Log::info("Solicitud Santander creada con exito : " . $flujo->ID);

                return [
                    "status" => "OK",
                    "message" => "Cuota calculada, Simulacion creada con exito",
                    "data" => $data,
                ];
            }
        } else {
            Log::error("Error al recibir respuesta solicitud Santander");
            return [
                "status" => "ERROR",
                "message" => "Error al recibir respuesta solicitud Santander",
                "data" => []
            ];
        }

    }

    public function homologacionModelos()
    {
        $solicitudCon = new ApiSolicitudController();

        $flujo = FLU_Flujos::where('Nombre', 'SANTANDER')->first();

        $referencia = $flujo->ID . date("Ymdh");

        $req = new Request();
        $req['referencia_id'] = $referencia;
        $req['proveedor_id'] = 8;
        $req['api_id'] = 6;
        $req['prioridad'] = 1;
        $req['flujoID'] = $flujo->ID;
        $req['onDemand'] = true;
        $req['data'] = ["id_table" => 38];

        $resp = $solicitudCon->store($req);

        $data = $solicitudCon->getData($resp);

        FLU_Notificaciones::Notificar($referencia, $flujo->ID);

        dd($data);

    }

    public function simulacionSantander($data)
    {
        $gastos = [
            [
                "id" => 24,
                "nombre" => "Gastos de Constitución"
            ]
        ];

        $seguros = [];
        // Seguro aplica a clientes dependientes e independientes
        if ($data["Cliente"]->TipoTrabajadorID == 2 || $data["Cliente"]->TipoTrabajadorID == 3) {
            $seguros = [
                [
                    "id" => 160,
                    "nombre" => "Seguro de Desgravamen"
                ]
            ];
        }


        $rut = $data["Cliente"]->Rut;
        $dataPayload = [
            "c_rut" => substr($rut, 0, strlen($rut) - 1),
            "c_dv" => substr($rut, -1, 1),
            "c_tipo_id" => "D", // D: Dependiente, E: Empresa, I: Independiente
            "c_pais_id" => "CHL",
            "c_ingreso" => $data["Cliente"]->SueldoLiquido,
            "v_org_id" => "2251",
            "v_estado_id" => "N", // N: Nuevo, U: Usado, SN: Seminuevo, NA: No aplica
            "v_uso_id" => "P", // C: Comercial, P: Particular, NA: NO aplica
            "v_marca_id" => $data["MarcaID"],
            "v_modelo_id" => $data["ModeloID"],
            "v_logica_modelo" => 3, // 1: Drive Nombre, 3: Drive ID
            "v_ano" => $data["Anno"],
            "v_precio" => $data["Precio"],
            "v_linea_id" => "N",
            "p_id" => 1,
            "p_pie" => $data["Pie"],
            "p_plazo" => $data["Plazo"],
            "p_vfmg" => 0,
            "p_fecha_primer_venc" => Carbon::create($data["Vencimiento"])->format("d-m-Y"),
            "p_g" => $gastos,
            "p_s" => $seguros,
            "p_m" => null,
        ];

//        dd($dataPayload);

        $solicitudCon = new ApiSolicitudController();

        $flujo = FLU_Flujos::where('Nombre', 'SANTANDER')->first();

        $referencia = $data["CotizacionID"];

        $req = new Request();
        $req['referencia_id'] = $referencia;
        $req['proveedor_id'] = 8;
        $req['api_id'] = 7;
        $req['prioridad'] = 1;
        $req['flujoID'] = $flujo->ID;
        $req['onDemand'] = true;
        $req['data'] = $dataPayload;

        $resp = $solicitudCon->store($req);

        $data = $solicitudCon->getData($resp);

        if ($data->errors) {
            Log::error("Error al crear solicitud Santander: " . $data->errors[0]->message);
            return [
                "status" => "ERROR",
                "message" => $data->errors[0]->message,
                "data" => []
            ];
        } else {
            FLU_Notificaciones::Notificar($referencia, $flujo->ID);
            Log::info("Solicitud Santander creada con exito : " . $flujo->ID);

            return [
                "status" => "OK",
                "message" => "Simulacion creada con exito, espere resultado de Solicitud de Credito",
                "data" => $data,
            ];
        }

    }


    public function creditoSantander(Request $request)
    {
        $data = $request->input("data");
        $idSimulacion = $data["idSimulacion"];
        $h = new FLU_Homologacion();

        $flujo = FLU_Flujos::where('Nombre', 'SANTANDER')->first();

        $fechaIngresoLaboral = Carbon::now('America/Santiago')
            ->subYears($data["antiguedadLaboral"])
            ->format('d-m-Y');

        $dataPayload = [
            "c_nombres" => $data["nombre"],
            "c_apellido_paterno" => $data["apellido"],
            "c_apellido_materno" => $data["segundoApellido"],
            "c_razon_social" => "",
            "c_nombre_fantasia" => "",
            "c_giro" => "",
            "c_genero_id" => $data["genero"],
            "c_fecha_nacimiento" => $data["fechaNacimiento"],
            "c_fecha_inicio_actividades" => null,
            "c_tipo_empresa_id" => null,
            "c_fecha_ingreso_laboral" => $fechaIngresoLaboral,
            "c_comuna_id" => $h->GetDato($data["comuna"], $flujo->ID, 'comuna', '13101'),
            "c_nacionalidad_id" => "CHL",
            "c_calle" => $data["direccion"],
            "c_calle_num" => "",
            "c_calle_resto" => "",
            "c_tipo_independiente_id" => null,
            "c_estado_civil_id" => $data["estadoCivil"],
            "c_nivel_educacional_id" => $data["nivelEducacion"],
            "c_email" => $data["email"],
            "c_telefonos" => $data["telefono"],
            "c_profesion" => $h->GetDato($data["profesion"], $flujo->ID, 'profesion', null),
            "c_actividadeconomica" => 61,
            "s_id" => $idSimulacion,
            "iva" => [],
            "dai" => [],
            "forma_pago" => [
                "tipo" => "OFP",
                "banco" => null,
                "t_cuenta" => null,
                "n_cuenta" => null,
                "n_tarjeta" => "",
            ],
            "rep_legal" => [],
            "socioaval" => null,
            "compra_para" => null,
        ];


        $solicitudCon = new ApiSolicitudController();

        $flujo = FLU_Flujos::where('Nombre', 'SANTANDER')->first();

        $referencia = $data["cotizacionID"];

        $req = new Request();
        $req['referencia_id'] = $referencia;
        $req['proveedor_id'] = 8;
        $req['api_id'] = 8;
        $req['prioridad'] = 1;
        $req['flujoID'] = $flujo->ID;
        $req['onDemand'] = false;
        $req['data'] = $dataPayload;

        $resp = $solicitudCon->store($req);


        Log::info("Solicitud Santander creada con exito : " . $flujo->ID);

        return [
            "status" => "OK",
            "message" => "Solicitud en tramite, espere resultado de Solicitud de Credito",
        ];

    }

// FIN CLASE
}
