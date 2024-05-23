<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiSolicitudCollection;
use App\Http\Resources\ApiSolicitudResource;
use App\Jobs\OrquestadorApi;
use App\Models\Api\ApiAutenticaciones;
use App\Models\Api\ApiProveedores;
use App\Models\Api\ApiSolicitudes;
use App\Models\FLU\FLU_Flujos;
use App\Models\FLU\FLU_Notificaciones;
use Carbon\Carbon;
use http\Params;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Mockery\Exception;
use SimpleXMLElement;
use SoapClient;
use SoapHeader;

class ApiSolicitudController extends Controller
{
    public function index()
    {
        return new ApiSolicitudCollection(
            ApiSolicitudes::paginate()
        );
    }

    public function remove_utf8_bom($text)
    {
        $bom = pack('H*', 'EFBBBF');
        $text = preg_replace("/^$bom/", '', $text);
        $text = preg_replace('/\x{FEFF}/u', '', $text);
        return $text;
    }

    public function show($solicitud)
    {
        return new ApiSolicitudCollection(
            ApiSolicitudes::where('ReferenciaID', $solicitud)
                ->orderBy('ID', 'Desc')->get()
        );
    }

    public function store(Request $request, $worker = null)
    {

        $solicitud = new ApiSolicitudes();

        DB::transaction(function () use ($request, $solicitud) {

            $solicitud->ReferenciaID = $request->input('referencia_id') ?? 0;
            $solicitud->ProveedorID = $request->input('proveedor_id');
            $solicitud->ApiID = $request->input('api_id');
            $solicitud->Prioridad = $request->input('prioridad');
            $solicitud->Peticion = (is_array($request->input('data'))) ? json_encode($request->input('data'), JSON_UNESCAPED_SLASHES) : $request->input('data');
            $solicitud->PeticionHeader = (is_array($request->input('dataHeader'))) ? json_encode($request->input('data')) : $request->input('dataHeader');
            $solicitud->FechaPeticion = Carbon::now();
            $solicitud->FlujoID = $request->input('flujoID');
            $solicitud->Reintentos = 3; //Numero de reintentos por default
            $solicitud->save();
        });

        // SI es OnDemand, ejecuta inmediatamente, sino, crea el JOB para ejecucion de cola
        if ($request->input('OnDemand')) {
            // resuelve inmediatamente la solicitud
            $resp = $this->resolverSolicitud($solicitud);

            return response()->json([
                'message' => $resp["message"],
                'success' => true,
                'status' => 'OK',
                'id' => $solicitud->id
            ], 200);

        } else {
            // crea el job para esta solicitud
            if ($worker)
                OrquestadorApi::dispatch($solicitud)->onQueue($worker);
            else
                OrquestadorApi::dispatch($solicitud);

            Log::info('Solicitud ID : ' . $solicitud->id . ' creada con exito');

            return response()->json([
                'message' => 'Solicitud ID : ' . $solicitud->id . ' creada con exito',
                'success' => true,
                'status' => 'OK',
                'id' => $solicitud->id
            ], 200);
        }

    }

    public function urlCallParam($url, $metodo, $params = [], $headers = [])
    {
        $response = Illuminate\Http\Client\Response::class;

        try {
            if ($metodo == 'POST') {
                $response = Http::asForm()
                    ->withHeaders($headers)
                    ->timeout(0)
                    ->withOptions([
                        'verify' => false,
                    ])
                    ->post($url, $params);
            } else if ($metodo == 'PUT') {
                $response = Http::withHeaders($headers)
                    ->timeout(0)
                    ->put($url, $params);
            } else if ($metodo == 'GET') {
                if ($params) {
                    $response = Http::withHeaders($headers)
                        ->withOptions([
                            'verify' => false,
                        ])
                        ->get($url, $params);
                } else {
                    $response = Http::withHeaders($headers)
                        ->withOptions([
                            'verify' => false,
                        ])
                        ->get($url);
                }
            }

            return [
                "status" => $response->status(),
                "response" => $response->json()
            ];
        } catch (Exception $e) {
            return [
                "status" => "ERROR",
                "response" => $e->getMessage(),
            ];
        }

    }

    public function urlCallJson($url, $metodo, $params = [], $headers = [], $usr = '', $pswd = '')
    {
        $response = Illuminate\Http\Client\Response::class;

        try {
            if ($metodo == 'POST') {
                if ($usr != '') {
                    $response = Http::asJson()
                        ->withHeaders($headers)
                        ->withBasicAuth($usr, $pswd)
                        ->timeout(0)
                        ->post($url, $params);
                } else {
                    $response = Http::asJson()
                        ->withHeaders($headers)
                        ->withOptions([
                            'verify' => false,
                        ])
                        ->acceptJson()
//                        ->withUserAgent('Mozilla/5.0')
                        ->timeout(0)
                        ->post($url, $params);
                }

            }

            return [
                "status" => $response->status(),
                "response" => $response->json()
            ];

        } catch (Exception $e) {
            Log::error("Error en llamada JSON : " . $e->getMessage());
            return [
                "status" => "ERROR",
                "response" => $e->getMessage(),
            ];
        }


    }

    public function urlCallSoap($url, $params = '', $headers = [])
    {
        $response = Illuminate\Http\Client\Response::class;

        try {
            Log::info("Resolviendo solicitud tipo XML");

            $xml = $this->remove_utf8_bom($params);

            $http = Http::withHeaders([
                'Content-Type' => 'application/xml',
                'charset' => 'utf-8'
            ])->withBody($xml, "text/xml")->post($url);

            $response = response($http->body())
                ->withHeaders([
                    'Content-Type' => 'text/xml'
                ]);

            return [
                "status" => $response->status(),
                "response" => $response->content()
            ];

        } catch (Exception $e) {
            return [
                "status" => "ERROR",
                "response" => $e->getMessage(),
            ];
        }


    }


    public function obtenerSolicitudes($limite)
    {
        $solicitudes = ApiSolicitudes::where("FechaResolucion", null)
            ->orderBy("FechaPeticion", 'DESC')
            ->orderBy("Prioridad", 'DESC')
            ->limit($limite)
            ->get();

        return $solicitudes;
    }

    public function obtenerLogin($ProveedorID)
    {

        // Obtener conexion activa, si existe.
        $estimado = Carbon::now()->addMinutes(5);
        $autenticacion = ApiAutenticaciones::where('ProveedorID', $ProveedorID)
            ->where('FechaExpiracion', '>', $estimado)
            ->first();

        // Si ya existe una coneccion activa (con un margen de 5 minutos)
        // se devuelve los datos de Tokens
        if ($autenticacion) {
            return [
                'success' => true,
                'status' => 'Existente',
                'token1' => $autenticacion->Token1,
                'token2' => $autenticacion->Token2,
            ];

            // Si no existe una coneccion activa. se genera una nueva.
        } else {

            $token1 = '';
            $token2 = '';
            $tiempoExpiracion1 = 0;
            $fechaExpiracion1 = '';
            $tiempoExpiracion2 = 0;
            $fechaExpiracion2 = '';

            // Primer paso, obtener en Providers el Endpoint de obtencion de Token -------------------------
            $tokenOauth = ApiProveedores::where("ProveedorID", $ProveedorID)
                ->where("Tipo", "auth1")->first();

            if ($tokenOauth) {
                if ($tokenOauth->TipoEntrada == "param") {

                    $params = explode(",", $tokenOauth->Params);
                    foreach ($params as $param) {
                        $explodedParam = explode(":", $param);
                        $endParams[trim($explodedParam[0])] = trim($explodedParam[1]);
                    }

                    $header = [];
                    if ($tokenOauth->Header != '') {
                        $params = explode(",", str_replace("'", "", $tokenOauth->Header));
                        foreach ($params as $param) {
                            $explodedParam = explode(":", $param);
                            $header[trim($explodedParam[0])] = trim($explodedParam[1]);
                        }
                    }

                    $result = $this->urlCallParam($tokenOauth->Url, $tokenOauth->Metodo, $endParams, $header);

//                    print_r ("Resultado Auth 1");
//                    print_r($result);

                } else if ($tokenOauth->TipoEntrada == "urlparam") {
                    $params = str_replace("\n", "", str_replace(",", "&", str_replace(":", "=", $tokenOauth->Params)));
                    $url = $tokenOauth->Url . "?" . $params;

//                    print_r ("Resultado Auth 1");
                    $result = $this->urlCallParam($url, $tokenOauth->Metodo, [], []);
//                    print_r($result);

                } else {
                    $result = $this->urlCallParam($tokenOauth->Url, $tokenOauth->Metodo, $tokenOauth->Json, []);
                }

                // Busca el dato en el indice marcado en el campo Token
                if (isset($result['response'][$tokenOauth->Token])) {
                    $token1 = $result['response'][$tokenOauth->Token];

                    if ($tokenOauth->IndiceExpiracion != '') {
                        $tiempoExpiracion1 = $result['response'][$tokenOauth->IndiceExpiracion];
                        $fechaExpiracion1 = Carbon::now()->addSeconds($tiempoExpiracion1);
                    } else {
                        $tiempoExpiracion1 = $tokenOauth->TiempoExpiracion;
                        $fechaExpiracion1 = Carbon::now()->addSeconds($tiempoExpiracion1);
                    }

                } else {
                    return [
                        "success" => false,
                        "message" => "hubo un problema al resolver la autenticacion"
                    ];
                }


                // Si existe un segundo paso de autenticacion.  -------------------------------------------
                $tokenAuth2 = ApiProveedores::where("ProveedorID", $ProveedorID)
                    ->where("Tipo", "auth2")->first();

                if ($tokenAuth2) {
                    if ($tokenAuth2->TipoEntrada == "json") {

                        $header = [];
                        if ($tokenOauth->Header != '') {
                            $params = explode(",", str_replace("'", "", $tokenOauth->Header));
                            foreach ($params as $param) {
                                $explodedParam = explode(":", $param);
                                $header[trim($explodedParam[0])] = trim($explodedParam[1]);
                            }
                        }
                        $header['Authorization'] = 'Bearer ' . $token1;

                        $result = $this->urlCallJson($tokenAuth2->Url, $tokenAuth2->Metodo,
                            json_decode($tokenAuth2->Json),
                            $header
                        );

                    }

                    if (isset($result['response'][$tokenAuth2->IndiceRespuesta][$tokenAuth2->Token])) {
                        $token2 = $result['response'][$tokenAuth2->IndiceRespuesta][$tokenAuth2->Token];

                        if ($tokenAuth2->IndiceExpiracion != '') {
                            $tiempoExpiracion2 = $result['response'][$tokenAuth2->IndiceExpiracion];
                            $fechaExpiracion2 = Carbon::now()->addSeconds($tiempoExpiracion2);
                        } else {
                            $tiempoExpiracion2 = $tokenAuth2->TiempoExpiracion;
                            $fechaExpiracion2 = Carbon::now()->addSeconds($tiempoExpiracion2);
                        }

                    } else {
                        return [
                            "success" => false,
                            "message" => "hubo un problema al resolver la autenticacion"
                        ];
                    }
                }

                // Guardar o actualizar la autenticacion
                ApiAutenticaciones::updateOrCreate(
                    ['ProveedorID' => $ProveedorID],
                    [
                        'Token1' => $token1,
                        'Token2' => $token2,
                        'Expiration' => ($tiempoExpiracion1 > $tiempoExpiracion2) ? $tiempoExpiracion2 : $tiempoExpiracion1,
                        'FechaInicio' => Carbon::now(),
                        'FechaExpiracion' => ($tiempoExpiracion1 > $tiempoExpiracion2) ? $fechaExpiracion2 : $fechaExpiracion1,
                        'Status' => 'activo'
                    ]
                );
            }

            return [
                'success' => true,
                'status' => 'Nuevo',
                'token1' => $token1,
                'token2' => $token2,
            ];

        }
    }

    public function resolverSolicitud($solicitud, $guardar = true)
    {

        // Obtiene solicitud a procesar
        /*$solicitud = ApiSolicitudes::where("id", $SolicitudID)
            ->first();*/
        $SolicitudID = $solicitud->id ?? 0;

        if ($solicitud) {
            if ($solicitud->FechaResolucion) {
                Log::info("Solicitud " . $solicitud->id . " ya ha sido resuelta");

                return [
                    "status" => "OK",
                    "message" => "Solicitud ya fue resuelta"
                ];

            } else {
                Log::info("Resolviendo solicitud : " . $SolicitudID);

                // Obtiene tokens de autenticacion
                $login = $this->obtenerLogin($solicitud->ProveedorID);

                if ($login['success']) {

                    // Obtener datos de la API a ejecutar

                    $API = ApiProveedores::with('respuestasTipo')
                        ->where("ProveedorID", $solicitud->ProveedorID)
                        ->where("id", $solicitud->ApiID)
                        ->first();

                    $header = []; // sin header por defecto

                    if ($API) {
                        $url = $API->Url;

                        if ($API->TipoEntrada == "param" || $API->TipoEntrada == "urlparam") {

                            $params = [];
                            if ($API->TipoEntrada == "param") {
                                if ($solicitud->Peticion != null) {
                                    if (json_validate($solicitud->Peticion)) {
                                        $peticionParams = json_decode($solicitud->Peticion, true);
                                        foreach ($peticionParams as $llave => $valor) {
                                            $params[$llave] = $valor;
                                        }
                                    } else {
                                        $peticionParams = explode(",", $solicitud->Peticion);
                                        foreach ($peticionParams as $param) {
                                            $explodedParam = explode(":", $param);
                                            $params[trim($explodedParam[0])] = trim($explodedParam[1]);
                                        }
                                    }

                                }
                            }

                            if ($API->TipoEntrada == "urlparam" && $API->Metodo == "GET") {
                                $paramsUrl = str_replace("\n", "", str_replace(",", "&", str_replace(":", "=", $API->Params)));
                                $url = $API->Url . "?" . $paramsUrl;

                                // Parametros URL
                                if ($solicitud->Peticion != null) {
                                    $urlExtra = str_replace(",", "&", $solicitud->Peticion);
//                                    $urlExtra = str_replace(":", "=", $urlExtra);
                                    $url = $url . "&" . $urlExtra;
                                }

                                Log::info("URL PARAM : " . $url);

                            }

                            // Param HEADERS
                            $header = [];

                            if ($solicitud->PeticionHeader != '') {
                                $paramsHeader = explode(",", str_replace("'", "", $solicitud->PeticionHeader));
                                foreach ($paramsHeader as $param) {
                                    $explodedParam = explode(":", $param);
                                    $header[trim($explodedParam[0])] = trim($explodedParam[1]);
                                }
                            }

                            // HEADERS

                            if ($API->Header != '') {
                                $paramsHeader = explode(",", str_replace("'", "", $API->Header));
                                foreach ($paramsHeader as $param) {
                                    $explodedParam = explode(":", $param);
                                    $header[trim($explodedParam[0])] = trim($explodedParam[1]);
                                }
                            }

                            if ($login['token1'] != '') {
                                $header['Authorization'] = 'Bearer ' . $login['token1'];
                            }
                            if ($API->Token != '') {
                                $header[$API->Token] = $login['token2'];
                            }
                            $respuesta = $this->urlCallParam($url, $API->Metodo,
                                $params,
                                $header
                            );

                        } else if ($API->TipoEntrada == "json") {

                            $header = [];
                            if ($API->Header != '') {
                                $params = explode(",", str_replace("'", "", $API->Header));
                                foreach ($params as $param) {
                                    $explodedParam = explode(":", $param);
                                    $header[trim($explodedParam[0])] = trim($explodedParam[1]);
                                }
                            }

//                            $header['Content-Type'] = 'application/json';
                            if ($login['token1'] != '') {
                                $header['Authorization'] = 'Bearer ' . $login['token1'];
                            }
                            if ($API->Token != '') {
                                $header[$API->Token] = $login['token2'];
                            }

                            $data = json_decode(str_replace("null", '""', $solicitud->Peticion));

                            $respuesta = $this->urlCalljson($url, $API->Metodo,
                                $data,
                                $header,
                                $API->User,
                                $API->Password
                            );

                        } else if ($API->TipoEntrada == "xml") {
                            $respuesta = $this->urlCallSoap($url, $solicitud->Peticion);

                            if (strpos($respuesta['response'], 'soap:Fault') !== false) {
                                Log::error("Error en respuesta SOAP");
                                $respuesta['status'] = 500;
                            }

                        }

                        // Si hay respuesta -----------------------------------
                        if ($respuesta) {

                            // Revisa si la respuesta es demasiado extensa. Y la guarda en un archivo
                            $serialized = serialize($respuesta["response"]);
                            if (function_exists('mb_strlen')) {
                                $size = mb_strlen($serialized, '8bit');
                            } else {
                                $size = strlen($serialized);
                            }

                            if ($size > 1024000) {
                                $dataFile = "public/data-" . $SolicitudID . ".json";
                                $d = Storage::put($dataFile, json_encode($respuesta["response"]));
                                $respuestaData = "file=" . $dataFile;
                            } else {
                                if (is_array($respuesta["response"])) {
                                    $respuestaData = json_encode($respuesta["response"]);

                                } else {
                                    $respuestaData = $respuesta["response"];

                                }
                            }


                            // Revisa las condiciones de exito
                            if ($API->IndiceExito != '') {
                                if (isset($respuesta["response"][$API->IndiceExito])) {
                                    $solicitud->Exito = 1;
                                } else {
                                    $solicitud->Exito = 0;
                                }
                            }

                            if ($respuesta["status"] > 202) {
                                $solicitud->Exito = 0;
                            } else {
                                $solicitud->Exito = 1;
                            }

                            // Si tiene respuestas tipo definidas. Revisa si hay errores o exitos
                            if ($API->respuestasTipo) {
//                                Log::info("Revisando respuestas tipo");

                                foreach ($API->respuestasTipo as $respuestaTipo) {
                                    $indices = explode(".", $respuestaTipo->llave, 2);

                                    // Revisa listado de errores de respuesta
                                    if (isset($respuesta["response"][$indices[0]])) {

                                        if (!is_array($respuesta["response"][$indices[0]])) {
                                            $respuesta["response"][$indices[0]] = [$respuesta["response"][$indices[0]]];
                                        }

                                        foreach ($respuesta["response"][$indices[0]] as $error) {

                                            if (count($indices) > 1) {
                                                $datoError = $error[$indices[1]];
                                            } else {
                                                $datoError = $error;
                                            }

                                            if (str_contains($datoError, $respuestaTipo->Mensaje)) {
                                                Log::info("Revisando error : " . $datoError . " - " . $respuestaTipo->Mensaje);

                                                if ($respuestaTipo->Tipo == 'ERROR') {
                                                    $solicitud->Exito = 0;
                                                    Log::info("Encontro tipo ERROR");
                                                } else {
                                                    $solicitud->Exito = 1;
                                                    break;
                                                    Log::info("Encontro tipo EXITO");
                                                }

                                                // Si respuesta marca reproceso
                                                if ($respuestaTipo->Reprocesa == 1) {
                                                    $solicitud->Reprocesa = 1;
                                                }
                                            }
                                        }
                                    }
                                }
                            }


                            $solicitud->Respuesta = $respuestaData;
                            $solicitud->FechaResolucion = Carbon::now()->toDateTimeString();
                            $solicitud->CodigoRespuesta = $respuesta['status'];


                            // Si la solicitud se debe guardar (por defecto)
                            if ($guardar) {
                                $solicitud->save();

                            }

                            //Notificacion
                            $ordenID = $solicitud->ReferenciaID;
                            $flujoID = $solicitud->FlujoID;
                            FLU_Notificaciones::Notificar($ordenID, $flujoID);

                            if ($solicitud->Exito == 0) {
                                Log::info("Solicitud " . $solicitud->id . " resuelta con errores");

                                return [
                                    "status" => "ERROR",
                                    "statusCode" => $respuesta['status'],
                                    "success" => false,
                                    "message" => "Solicitud " . $solicitud->id . " resuelta con errores",
                                    "response" => ""
                                ];

                            } else {
                                Log::info("Solicitud " . $solicitud->id . " resuelta con exito");


                                return [
                                    "status" => "OK",
                                    "statusCode" => $respuesta['status'],
                                    "success" => true,
                                    "message" => "Solicitud " . $solicitud->id . " resuelta con exito",
                                    "response" => $respuestaData
                                ];
                            }

                        }

                    } else {
                        return [
                            "status" => "ERROR",
                            "success" => false,
                            "message" => "No existe proveedor"
                        ];
                    }
                } else {
                    return [
                        "status" => "ERROR",
                        "success" => false,
                        "message" => "No se pudo realizar el login"
                    ];
                }
            }

        } else {
            return [
                "status" => "ERROR",
                "success" => false,
                "message" => "No existe solicitud"
            ];
        }

        return true;
    }

    public static function reprocesarJob($solicitud)
    {
//        $solicitud = ApiSolicitudes::find($solicitudID);
        Log::info("Reprocesando solicitud " . $solicitud->ID);
        $solicitud->FechaResolucion = null;
        $solicitud->Exito = 0;
        $solicitud->CodigoRespuesta = 0;
        $solicitud->save();

        OrquestadorApi::dispatch($solicitud);
    }

    public function getData($resp)
    {
        $resp = $resp->getData();

        $solicitud = ApiSolicitudes::where('id', $resp->id)->first();

        if (substr($solicitud->Respuesta, 0, 4) == 'file') {
            $nombre = substr($solicitud->Respuesta, 5, strlen($solicitud->Respuesta));
            $arrayData = json_decode(Storage::get($nombre));
        } else {
            $arrayData = json_decode($solicitud->Respuesta);
        }

        return $arrayData;
    }

    public function getSolicitudesFlujo(Request $request)
    {
        $refID = $request->input('ReferenciaID');

        /*$flujos = ApiSolicitudes::where('ReferenciaID', $refID)
            ->groupBy(['ReferenciaID','ApiID'])
            ->orderBy('ID', 'DESC');
        dd($flujos);*/

        $flujos = FLU_Flujos::withWhereHas('solicitudes', function (Builder $query) use ($refID) {
            return $query->where('API_Solicitudes.ReferenciaID', $refID)
                ->groupBy(['API_Solicitudes.ReferenciaID', 'API_Solicitudes.ApiID'])
                ->orderBy('API_Solicitudes.ID', 'DESC');
        })->where('Nombre', 'SANTANDER')
            ->get();
//        dd($flujos);

        $data = [];
        foreach ($flujos as $flujo) {
            foreach ($flujo->solicitudes as $solicitud) {
                $data[$flujo->Nombre][] = $solicitud->toArray();
            }
        }

        return $data;
    }


    public function execute(Request $request, $worker = null)
    {

        $solicitud = new ApiSolicitudes();

        Log::info("Ejecutando solicitud sin guardado");

        DB::transaction(function () use ($request, $solicitud) {

            $solicitud->ReferenciaID = $request->input('referencia_id') ?? 0;
            $solicitud->ProveedorID = $request->input('proveedor_id');
            $solicitud->ApiID = $request->input('api_id');
            $solicitud->Prioridad = $request->input('prioridad');
            $solicitud->Peticion = (is_array($request->input('data'))) ? json_encode($request->input('data'), JSON_UNESCAPED_SLASHES) : $request->input('data');
            $solicitud->PeticionHeader = (is_array($request->input('dataHeader'))) ? json_encode($request->input('data')) : $request->input('dataHeader');
            $solicitud->FechaPeticion = Carbon::now();
            $solicitud->FlujoID = $request->input('flujoID');
            $solicitud->Reintentos = 3; //Numero de reintentos por default
        });

        // SI es OnDemand, ejecuta inmediatamente, sino, crea el JOB para ejecucion de cola
        // resuelve inmediatamente la solicitud
        $resp = $this->resolverSolicitud($solicitud, false);

        $response = [
            'message' => $resp["message"],
            'success' => true,
            'status' => 'OK',
            'statusCode' => $resp["status"],
            'response' => $resp["response"]
        ];
        Log::info("Respuesta : " . json_encode($resp["response"]));

        return response()->json($response, 200);

    }

}
