<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiProd\FLujoHomologacionController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Logger;
use App\Http\Resources\LeadCollection;
use App\Http\Resources\LeadResource;
use App\Models\Api\ApiSolicitudes;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\CC\CC_AsignacionLeadGenesys;
use App\Models\Client;
use App\Models\FLU\FLU_Homologacion;
use App\Models\FLU\FLU_Notificaciones;
use App\Models\Lead;
use App\Models\LOG_IntegracionLeads;
use App\Models\MA\MA_Clientes;
use App\Models\MA\MA_Gerencias;
use App\Models\MA\MA_Marcas;
use App\Models\MA\MA_Modelos;
use App\Models\MA\MA_SubOrigenes;
use App\Models\MA\MA_Sucursales;
use App\Models\MA\MA_Usuarios;
use App\Models\MK\MK_Leads;
use App\Models\MK\MK_LeadsEstados;
use App\Models\SIS\SIS_Agendamientos;
use App\Models\TDP\TDP_FacebookSucursales;
use App\Models\TDP\TDP_WebPompeyoSucursales;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Exception;

/**
 * @OA\Info(title="API Pompeyo", version="1.0")
 *
 * @OA\Server(url="https://apifrontend.pompeyo.cl/")
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="apiKey",
 *     name="Authorization",
 *     in="header",
 * ),
 */
class LeadController extends Controller
{
    /**
     * Muestra los registros de Leads
     * @OA\Get(
     *     path="/api/leads",
     *     tags={"Leads"},
     *     summary="Mostrar Leads",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Mostrar todos los leads."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */

    public function index()
    {
        return new LeadCollection(
            Lead::with('user:ID,Rut,Nombre,Apellido,SegundoApellido,Email,Telefono', 'branchOffice:ID,Sucursal', 'origin:ID,Origen')
                ->paginate()
        );
    }

    /**
     * Muestra el registro solicitado.
     * @param int $id
     * @return \Illuminate\Http\Response
     * @OA\Get(
     *     path="/api/lead/{lead}",
     *     tags={"Lead"},
     *     summary="Mostrar informacion de un Lead",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         description="Parámetro necesario para la consulta de datos de un Lead",
     *         in="path",
     *         name="lead",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="int", value="1", summary="Introduce un número de id de Lead.")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mostrar info de un Lead."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se ha encontrado el Lead."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function show(Lead $lead)
    {
        return new LeadResource($lead);
    }

    /**
     * Registro de Lead
     * @OA\Post(
     *     path="/api/lead",
     *     tags={"Registrar Lead"},
     *     summary="Registrar info de un Lead",
     *     security={{ "bearerAuth": {} }},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                      property="id",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="nombre",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="segundoNombre",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="apellido",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="segundoApellido",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="rut",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="telefono",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property ="fechaNacimiento",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property ="direccion",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="lead",
     *
     *                  ),
     *                 example={"data" : { "nombre": "Pedro","segundoNombre": "Juan","apellido": "Perez","segundoApellido": "Muñoz","rut": "11111111","email": "contacto@email.com","telefono": "123456789","fechaNacimiento": "1980-01-01","direccion": "Alameda 1000","lead": {"origenID": 2,"subOrigenID": 20,"sucursalID": 72,"vendedorID": 1204,"marca": "PEUGEOT","modelo": "PARTNER"}}}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lead insertado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Recurso no encontrado."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function store(Request $request)
    {
        Log::info("Inicio creacion LEAD");

        $tlds_comunes = array(
            "gTLD" => array(
                ".com",  // Comercial
                ".org",  // Organizaciones
                ".net",  // Redes
                ".info", // Información
                ".biz",  // Negocios
                ".gov",  // Gobierno
                ".edu",  // Educación
                ".mil",  // Militar
                ".name", // Personas/individuos
                ".pro"   // Profesionales
            ),
            "ccTLD" => array(
                ".us",   // Estados Unidos
                ".uk",   // Reino Unido
                ".ca",   // Canadá
                ".de",   // Alemania
                ".fr",   // Francia
                ".es",   // España
                ".it",   // Italia
                ".au",   // Australia
                ".mx",   // México
                ".br"    // Brasil
            ),
            "nTLD" => array(
                ".tech", // Tecnología
                ".ai",   // Inteligencia Artificial
                ".online", // Actividades en línea
                ".store", // Tiendas en línea
                ".app",  // Aplicaciones
                ".club", // Clubes/comunidades
                ".xyz"   // Genérico y versátil
            )
        );


        $tlds = array(
            "com", "org", "net", "int", "edu", "gov", "mil", "co", "info", "name",
            "pro", "aero", "museum", "cat", "coop", "tel", "biz", "us", "eu", "asia",
            "jobs", "mobi", "xxx", "post", "int", "arpa", "test", "gov", "edu", "coop",
            // ccTLDs (country code TLDs)
            "ad", "ae", "af", "ag", "ai", "al", "am", "ao", "aq", "ar", "as", "at", "au",
            "aw", "ax", "az", "ba", "bb", "bd", "be", "bf", "bg", "bh", "bi", "bj", "bl",
            "bm", "bn", "bo", "bq", "br", "bs", "bt", "bv", "bw", "by", "bz", "ca", "cc",
            "cd", "cf", "cg", "ch", "ci", "ck", "cl", "cm", "cn", "co", "cr", "cu", "cv",
            "cw", "cx", "cy", "cz", "de", "dj", "dk", "dm", "do", "dz", "ec", "ee", "eg",
            "eh", "er", "es", "et", "fi", "fj", "fm", "fo", "fr", "ga", "gb", "gd", "ge",
            "gf", "gg", "gh", "gi", "gl", "gm", "gn", "gp", "gq", "gr", "gs", "gt", "gu",
            "gw", "gy", "hk", "hm", "hn", "hr", "ht", "hu", "id", "ie", "il", "im", "in",
            "io", "iq", "ir", "is", "it", "je", "jm", "jo", "jp", "ke", "kf", "kg", "kh",
            "ki", "kj", "kk", "kl", "km", "kn", "kp", "kr", "kw", "ky", "kz", "la", "lb",
            "lc", "li", "lk", "lr", "ls", "lt", "lu", "lv", "ly", "ma", "mc", "md", "me",
            "mf", "mg", "mh", "mk", "ml", "mm", "mn", "mo", "mp", "mq", "mr", "ms", "mt",
            "mu", "mv", "mw", "mx", "my", "mz", "na", "nc", "ne", "nf", "ng", "ni", "nl",
            "no", "np", "nr", "nu", "nz", "om", "pa", "pe", "pf", "pg", "ph", "pk", "pl",
            "pm", "pn", "pr", "ps", "pt", "pw", "py", "qa", "re", "ro", "rs", "ru", "rw",
            "sa", "sb", "sc", "sd", "se", "sg", "sh", "si", "sj", "sk", "sl", "sm", "sn",
            "so", "sr", "ss", "st", "sv", "sx", "sy", "sz", "tc", "td", "tf", "tg", "th",
            "tj", "tk", "tl", "tm", "tn", "to", "tr", "tt", "tv", "tz", "ua", "ug", "uk",
            "us", "uy", "uz", "va", "vc", "ve", "vg", "vi", "vn", "vu", "wf", "ws", "ye",
            "yt", "za", "zm", "zw"
        );


        try {
            DB::transaction(function () use ($request) {

                $client = new Client();
                $client->FechaCreacion = date('Y-m-d H:i:s');
                $client->EventoCreacionID = 1;
                $client->UsuarioCreacionID = 1683; // Usuario por defecto de Integracion - Web Pompeyo
                $client->Nombre = $request->input('data.nombre');
                $client->SegundoNombre = $request->input('data.segundoNombre');
                $client->Apellido = $request->input('data.apellido');
                $client->SegundoApellido = $request->input('data.segundoApellido');
                $client->Rut = $request->input('data.rut');
                $client->Email = $request->input('data.email');
                $client->Telefono = $request->input('data.telefono');
                $client->FechaNacimiento = $request->input('data.fechaNacimiento');
                $client->Direccion = $request->input('data.direccion');
                $client->save();

                $brand = Brand::select('ID')->where('Marca', $request->input('data.lead.marca'))->get()->collect();
                $carModel = CarModel::select('ID')->where('MODELO', $request->input('data.lead.modelo'))->get()->collect();

                $lead = new Lead();
                $lead->FechaCreacion = date('Y-m-d H:i:s');
                $lead->EventoCreacionID = 1;
                $lead->UsuarioCreacionID = 1204;
                $lead->OrigenID = $request->input('data.lead.origenID'); // id por consumidor API
                $lead->SubOrigenID = $request->input('data.lead.subOrigenID'); // id por consumidor API
                $lead->ClienteID = $client->ID;
                $lead->CampanaId = $request->input('data.lead.campana');
                $lead->SucursalID = $request->input('data.lead.sucursalID'); // htext
                $lead->VendedorID = $request->input('data.lead.vendedorID');
                $lead->MarcaID = $brand->count() > 0 ? $brand[0]['ID'] : null;
                $lead->ModeloID = $carModel->count() > 0 ? $carModel[0]['ID'] : null;

                $lead->Nombre = $request->input('data.nombre');
                $lead->SegundoNombre = $request->input('data.segundoNombre');
                $lead->Apellido = $request->input('data.apellido');
                $lead->SegundoApellido = $request->input('data.segundoApellido');
                $lead->Rut = $request->input('data.rut');
                $lead->Email = $request->input('data.email');
                $lead->Telefono = $request->input('data.telefono');
                $lead->FechaNacimiento = $request->input('data.fechaNacimiento');
                $lead->Direccion = $request->input('data.direccion');
                $lead->ComunaID = 1;

                $lead->save();
            });
            return response()->json(['messages' => 'Leads creado correctamente'], 200);

        } catch (Exception $e) {
//            return $e->getMessage();

        }

        return true;
    }

    public function nuevoLead(Request $request)
    {
        $Log = new Logger();
        $Log->info("Inicio creacion LEAD");
        $logArray = [];
        $rut = null;

        try {

            // UsuarioID ---------------------
            $usuarioID = $request->input('data.usuarioID');
            if (!$usuarioID) {
                $Log->info("No se ha enviado usuarioID");

                $Log->info("Asignando usuario por defecto");
                $usuarioID = 2893; // INTEGRACION FACEBOOK

            }

            // Variables y validaciones
            $comentario = $request->input('data.lead.comentario');
            $sucursalID = $request->input('data.lead.sucursalID') ?? null;
            $sucursalNombre = $request->input('data.lead.sucursal') ?? null;
            $marcaID = $request->input('data.lead.marcaID') ?? null;
            $marcaNombre = $request->input('data.lead.marca') ?? null;
            $modeloID = $request->input('data.lead.modeloID') ?? null;
            $modeloNombre = $request->input('data.lead.modelo') ?? null;
            $origen = $request->input('data.lead.origen') ?? null;
            $origenIngreso = $request->input('data.lead.origenIngreso') ?? 1;
            $origenID = $request->input('data.lead.origenID') ?? 3;
            $subOrigenID = $request->input('data.lead.subOrigenID') ?? 1;
            $idFlujo = $request->input('data.lead.idFlujo') ?? 0;
            $fuente = $request->input('data.fuente') ?? 1;
            $idExterno = $request->input('data.lead.externalID') ?? null;
            $idExternoSecundario = $request->input('data.lead.externalIDSecundario') ?? null;
            $idHubspot = $request->input('data.lead.idHubspot') ?? null;
            $visible = $request->input('data.lead.visible') ?? 1; // 1 = visible, 0 = no visible
            $lead = null;
            $actualizaEstado = $request->input('data.lead.actualizaEstado') ?? 0; // 1 = si, 0 = no

            $financiamiento = $request->input('data.lead.financiamiento') ?? 2;
            if ($financiamiento == 'SI') {
                $financiamiento = 1;
            } else if ($financiamiento == 'NO') {
                $financiamiento = 2;
            }

//            Validaciones previas
            $procesar = true;

            // Si es Hubspot
            if ($fuente == 2) {
                $lead = MK_Leads::select('ID')->where('IDHubspot', $request->input('data.lead.externalID'))->first();
                // si existe, no se crea
                if ($lead) {
                    Log::notice('Lead ya existe en BD Roma : ' . $lead->ID);
                    $procesar = false;
                }
            } else {
                $lead = MK_Leads::select('ID')->where('IDExterno', $request->input('data.lead.externalID'))->first();
                // si existe, no se crea
                if ($lead) {
                    Log::notice('Lead ya existe en BD Roma : ' . $lead->ID);
                    $procesar = false;
                }
            }


            if ($procesar === true) {
                // RUT ---------------------
                if ($request->input('data.rut')) {
                    $rut = substr($request->input('data.rut'), 0, 15);
                    $rut = str_replace('.', '', $rut);
                    $rut = str_replace('-', '', $rut);
                    $rut = str_replace(' ', '', $rut);
                }

                // CLIENTE ---------------------
                $objCliente = new MA_Clientes();
//                Log::info("Buscando cliente (rut : " . $request->input('data.rut') . ", nombre:" . ($request->input('data.nombre') ?? '') . ")");

                if ($rut) {
                    $cliente = $objCliente->where('Rut', $rut)->first();
                } else if ($request->input('data.email')) {
                    $cliente = $objCliente->where('Email', $request->input('data.email'))->first();
                } else if ($request->input('data.telefono')) {
                    $cliente = $objCliente->where('Telefono', $request->input('data.telefono'))->first();
                } else {
                    Log::error("No se pudo procesar el Negocio : " . $request->input('data.lead.externalID') . " | No se encontro la informacion minima de nombre, rut o telefono");
                    return response()->json(['status' => false, 'messages' => 'Ha ocurrido un error en la creacion de Lead'], 500);
                }

                // Si no encuentra cliente, se crea uno nuevo si es que trae rut
                if (!$cliente) {

                    // Si es un rut valido- se crea el cliente
                    if ($rut != '') {
                        $objCliente->Rut = $rut;
                        $objCliente->FechaCreacion = date('Y-m-d H:i:s');
                        $objCliente->EventoCreacionID = 1;
                        $objCliente->UsuarioCreacionID = $usuarioID; // 2824
                        $objCliente->Nombre = $request->input('data.nombre') ?? '';
                        $objCliente->SegundoNombre = $request->input('data.segundoNombre') ?? '';
                        $objCliente->Apellido = $request->input('data.apellido') ?? '';
                        $objCliente->SegundoApellido = $request->input('data.segundoApellido') ?? '';
                        $objCliente->Email = $request->input('data.email') ?? '';
                        $objCliente->Telefono = $request->input('data.telefono') ?? '';
                        $objCliente->FechaNacimiento = $request->input('data.fechaNacimiento') ?? null;
                        $objCliente->Direccion = $request->input('data.direccion') ?? '';

                        $objCliente->save();

                        $Log->notice("Cliente creado: " . $objCliente->ID);
                        $cliente = $objCliente;
                    } else {
                        $Log->error("No se creo el cliente, rut invalido o no existente");
                        $cliente = null;
                    }
                } else {
                    $Log->info("Cliente encontrado: " . $cliente->Nombre . " ID: " . $cliente->ID);
                    if ($rut != '' && $rut != $cliente->Rut) {
                        $cliente->Rut = $rut;
                        $Log->info("Rut actualizado :" . $rut);
                    }
                    if ($request->input('data.nombre') != '' && $request->input('data.nombre') != $cliente->Nombre) {
                        $cliente->Nombre = $request->input('data.nombre');
                        $Log->info("Nombre actualizado :" . $request->input('data.nombre'));
                    }
                    if ($request->input('data.email') != '' && $request->input('data.email') != '' && $request->input('data.email') != $cliente->Email) {
                        $cliente->Email = $request->input('data.email');
                        $Log->info("Email actualizado :" . $request->input('data.email'));
                    }
                    if ($request->input('data.telefono') != '' && $request->input('data.telefono') != $cliente->Telefono) {
                        $cliente->Telefono = $request->input('data.telefono');
                        $Log->info("Telefono actualizado :" . $request->input('data.telefono'));
                    }

                    $cliente->save();
                }


                // CREACION DE LEAD ---------------------
                $vendedorID = 1;

                // Asignacion vendedor desde Genesys
                $asignacion = CC_AsignacionLeadGenesys::where('GerenciaID',
                    $request->input('data.lead.marcaID'))
                    ->where('Activo', 1)
                    ->first();

                $vendedorID = $request->input('data.lead.vendedorID') ?? 1;

                if ($asignacion && $vendedorID == 0) {
                    $Log->info("Asignacion de vendedor desde Genesys: " . $asignacion->VendedorID);
                    $vendedorID = $asignacion->VendedorID;
                }
                // ---------------------

                $returnMessage = "Lead creado correctamente";

                // REGISTRO LOG ---------------------
                LOG_IntegracionLeads::info(
                    [
                        'Fecha' => date('Y-m-d H:i:s'),
                        'Rut' => $rut,
                        'Nombre' => $request->input('data.nombre'),
                        'Email' => $request->input('data.email'),
                        'Telefono' => $request->input('data.telefono'),
                        'Modelo' => $modeloID ?? $modeloNombre,
                        'Version' => '',
                        'Sucursal' => $sucursalID ?? $sucursalNombre,
                        'Origen' => $origenID ?? $origen,
                        'SubOrigen' => $subOrigenID,
                        'IdExterno' => $request->input('data.lead.externalID'),
                        'SP' => 'API',
                    ]
                );

                // HOMOLOGACIONES ---------------------

                $gerenciaHomologada = 0;

                // Homologacion Marca
                if (!$marcaID && $marcaNombre && $idFlujo) {
                    $marcaHomologada = FLU_Homologacion::GetDato(
                        $marcaNombre,
                        $idFlujo,
                        'marca',
                        1
                    );

                    if ($marcaHomologada == 1) {
                        $marca = MA_Gerencias::where('Gerencia', 'like', $marcaNombre)->first();
                        if ($marca) {
                            $marcaHomologada = $marca->MarcaAsociada;
                            $gerenciaHomologada = $marca->ID;

                            $Log->info("Marca encontrada : " . $marca->Gerencia . " (" . $marcaHomologada . ") gerenciaID : " . $gerenciaHomologada);

                            if ($marcaHomologada == 0) {
                                $marcaHomologada = 1; // Sin Info
                            }
                        } else {
                            $Log->warning("Marca no encontrada");
                        }
                    } else {
                        $Log->info("Homologacion de marca encontrada: " . $marcaNombre . " (" . $marcaHomologada . ")");
                        $marcaID = 0;
                    }

                    if (!$marcaID) {
                        $marcaID = $marcaHomologada;
                    }

                    // Guardamos marca y modelo en el comentario, en caso de que falle la homologacion.
                    $comentario = "Marca: " . $request->input('data.lead.marca') . " " . $comentario;
                }


                // Homologacion Modelo
                if (!$modeloID && $modeloNombre && $idFlujo) {
                    $modeloHomologado = FLU_Homologacion::GetDato(
                        $modeloNombre,
                        $idFlujo,
                        'modelo', 1
                    );

                    if ($modeloHomologado == 1) {
                        $modelo = MA_Modelos::where('Modelo', 'like', $modeloNombre)
                            ->orWhere('H_Texto', 'like', $modeloNombre)
                            ->where('Activo', 1)
                            ->first();

                        if ($modelo) {
                            $Log->info("Modelo encontrado: " . $modelo->Modelo);
                            $modeloHomologado = $modelo->ID;
                            if ($marcaID == 0) {
                                $marcaID = $modelo->MarcaID;
                            }
                        } else {
                            $Log->warning("Modelo no encontrado");

                        }
                    } else {
                        $Log->info("Homologacion de modelo encontrada: " . $modeloNombre . " (" . $modeloHomologado . ")");
                    }

                    if (!$modeloID) {
                        $modeloID = $modeloHomologado;
                    }
                    $comentario = "Modelo: " . $modeloNombre . " " . $comentario;

                } else if ($marcaID == 0 && $modeloID != 0) {
                    $modelo = MA_Modelos::where('ID', $modeloID)->first();
                    $marcaID = $modelo->MarcaID;
                    $Log->info("Marca asignada : " . $marcaID);
                }


                // Homlogacion Sucursal
                if (!$sucursalID && $sucursalNombre && $idFlujo) {

                    // Busca en Tabla Homologacion
                    $sucursalHomologada = FLU_Homologacion::GetDato(
                        $sucursalNombre,
                        $idFlujo,
                        'sucursal',
                        1 // Valor no encontrado
                    );

                    if ($sucursalHomologada == 1) {
                        $sucursal = MA_Sucursales::where('Sucursal', $sucursalNombre)
                            ->where('Activa', 1)
                            ->first();

                        if ($sucursal) {
                            $Log->info("Sucursal encontrada: " . $sucursal->Sucursal);
                            $sucursalHomologada = $sucursal->ID;

                            /*if($sucursal->gerencia->MarcaAsociada != $marcaID){
                                $marcaID = $sucursal->gerencia->MarcaAsociada;
                                $Log->warning("Marca de sucursal no coincide con marca de lead. Se asigna marca de sucursal: " . $marcaID);
                            }*/

                        } else {
                            $Log->warning("Sucursal no encontrada, Buscando sucursal Facebook");
                            $sucursalFB = TDP_FacebookSucursales::where('Sucursal', $sucursalNombre)
                                ->where('GerenciaID', $gerenciaHomologada)
                                ->first();
                            if ($sucursalFB) {
                                $Log->info("Sucursal Facebook encontrada: " . $sucursalFB->Sucursal);
                                $sucursalHomologada = $sucursalFB->SucursalID;
                            } else {
                                $Log->warning("Sucursal Facebook no encontrada. Buscando sucursal Web");

                                $sucursalWeb = TDP_WebPompeyoSucursales::where('Sucursal', $sucursalNombre)
                                    ->orWhere('Sucursal', str_replace(" ", "_", $sucursalNombre))
                                    ->first();
                                if ($sucursalWeb) {
                                    $Log->info("Sucursal Web encontrada: " . $sucursalWeb->Sucursal);
                                    $sucursalHomologada = $sucursalWeb->SucursalID;
                                } else {
                                    $Log->error("Sucursal Web no encontrada");
                                }
                            }
                        }
                    } else {
                        $Log->info("Homologacion de sucursal encontrada: " . $sucursalNombre . " (" . $sucursalHomologada . ")");
                    }

                    if (!$sucursalID) {
                        // SI se encontro alguna sucursal.
                        if ($sucursalHomologada > 1) {
                            $sucursalID = $sucursalHomologada;
                        } // SI no se encontro sucursal, se toma la primera sucursal de la Marca / Gerencia
                        else {
                            $Log->info("No se encontro sucursal. Buscando sucursal por Marca / Gerencia (" . $marcaID . " / " . $gerenciaHomologada . ")");
                            if ($gerenciaHomologada > 0) {
                                $sucursalDefecto = MA_Sucursales::where('GerenciaID', $gerenciaHomologada)->first();
                                $sucursalID = $sucursalDefecto->ID;
                                $Log->info("Sucursal por Marca asignada : " . $sucursalDefecto->Sucursal);
                            } else {
                                $sucursalID = 1;
                                $Log->error("No se encontro sucursal por Marca. Se asigna sucursal 1");
                            }

                        }
                    }
                }


                // Homologacion Origen
                if ($origen != null) {
                    $alias = MA_SubOrigenes::where('Alias', $origen)->first();
                    if ($alias) {
                        $origenID = $alias->OrigenID;
                        $subOrigenID = $alias->ID;
                    } else {
                        $origenID = 3;
                        $subOrigenID = 1;
                    }
                }

                // Correccion de origen de Lead (Facebook marketing)
                if ($origenID == 8 && $subOrigenID == 15) {
                    $subOrigenID = 36;
                    $usuarioID = 2893; // INTEGRACION FACEBOOK
                }

                // Link de conversacion RELIF
                $linkInteres = $request->input('data.lead.link') ?? null;

                // Verificacion de existencia de Lead
                $lead = MK_Leads::where('IDExterno', $idExterno)
                    ->where('OrigenID', $origenID)
                    ->where('ModeloID', $modeloID)
                    ->where('FechaCreacion', '>', date('Y-m-d H:i:s', strtotime('-1 day')))
                    ->first();

                if ($lead) {
                    $Log->info("Lead ya existe: " . $lead->ID);
                }

                // Creacion de Lead --------------------------------
                $fechaCreacion = Carbon::now();
                $fechaRevision = Carbon::now();
                $fechaFinJornada = Carbon::createFromTimeString('18:30:00');
                $fechaFinDia = Carbon::createFromTimeString('23:59:59');
                $fechaInicioDia = Carbon::createFromTimeString('09:00:00');


                // Si se crea posterior a las 18.30 y antes de las 00:00
                if ($fechaCreacion > $fechaFinJornada && $fechaCreacion < $fechaFinDia) {
                    $fechaRevision = $fechaRevision->addDay();
                    $fechaRevision = $fechaRevision->format("Y-m-d 09:00:00");
                }

                // Si se crea posterior a las 18.30 y antes de las 23.59
                if ($fechaCreacion < $fechaInicioDia) {
                    $fechaRevision = $fechaRevision->format("Y-m-d 09:00:00");
                }

                $fechaCreacion = $fechaCreacion->format('Y-m-d H:i:s');

                if($request->input('data.lead.estado') != "") {
                    $estadoID = MK_LeadsEstados::where('Estado', $request->input('data.lead.estado'))->first();
                    if ($estadoID) {
                        $estadoID = $estadoID->ID;
                    } else {
                        $estadoID = 1; // Estado Activo
                    }
                } else {
                    $estadoID = 1; // Estado Activo
                }


                $lead = new MK_Leads();
                $lead->FechaCreacion = $fechaCreacion;
                $lead->FechaCreacionHorasHab = $fechaRevision;
                $lead->EventoCreacionID = 1;
                $lead->UsuarioCreacionID = $usuarioID;
                $lead->OrigenID = $origenID;
                $lead->OrigenIngreso = $origenIngreso;
                $lead->SubOrigenID = $subOrigenID;
                $lead->IntegracionID = $fuente;

                $lead->ClienteID = $cliente->ID ?? 1;
                $lead->Rut = $rut ?? '';
                $lead->Nombre = $request->input('data.nombre') ?? '';
                $lead->SegundoNombre = $request->input('data.segundoNombre') ?? '';
                $lead->Apellido = $request->input('data.apellido') ?? '';
                $lead->SegundoApellido = $request->input('data.segundoApellido') ?? '';
                $lead->Email = $request->input('data.email') ?? '';
                $lead->Telefono = $request->input('data.telefono') ?? '';
                $lead->FechaNacimiento = $request->input('data.fechaNacimiento') ?? null;
                $lead->Direccion = $request->input('data.direccion') ?? '';
                $lead->ComunaID = 1;
                $lead->EstadoID = $estadoID; // Estado Activo
                $lead->LogEstado = $actualizaEstado;

                $lead->CampanaId = $request->input('data.lead.campana') ?? null;
                $lead->SucursalID = $sucursalID; // htext

                // verificacion de vendedor en caso que se envie uno especifico. (debe existir)

                if ($vendedorID > 1) {
                    Log::info("Verificando vendedor enviado : " . $vendedorID);

                    $vendedor = MA_Usuarios::where('ID', $vendedorID)->first();
                    if (!$vendedor) {
                        $vendedorID = 1;
                        Log::error('Vendedor enviado, no existe ' . $vendedorID);
                    } else {
                        Log::notice("Vendedor encontrado" . $vendedor->Nombre . " " . $vendedor->Apellido);
                    }
                } else {
                    $vendedorID = $usuarioID;
                }
                $lead->VendedorID = $vendedorID;

                $lead->MarcaID = $marcaID ?? 1;
                $lead->ModeloID = $modeloID ?? 1;
                $lead->IDExterno = $idExterno;
                $lead->IDExternoSecundario = $idExternoSecundario;
                $lead->IDHubspot = $idHubspot;
                $lead->Comentario = $comentario ?? null;
                $lead->Financiamiento = $financiamiento;
                $lead->LinkInteres = $linkInteres;
                $lead->OrigenIngreso = 1; // canal API
                $lead->Visible = $visible; // 1 = visible, 0 = no visible

                $lead->save();
                $returnMessage = "Lead creado correctamente";

                $Log->info("LEAD " . $lead->ID . " creado con exito");


                // Creacion de Solicitud API, para registro (No se puede reprocesar) ----

                $solicitud = ApiSolicitudes::create([
                    'FechaCreacion' => date('Y-m-d H:i:s'),
                    'EventoCreacionID' => 1,
                    'UsuarioCreacionID' => $usuarioID,
                    'ReferenciaID' => $lead->ID,
                    'ProveedorID' => 6,
                    'ApiID' => 0,
                    'Prioridad' => 1,
                    'Peticion' => json_encode($request->input()),
                    'CodigoRespuesta' => 200,
                    'Respuesta' => json_encode(['status' => true,
                        'messages' => 'Lead creado correctamente',
                        'LeadID' => $lead->ID]),
                    'FechaPeticion' => date('Y-m-d H:i:s'),
                    'FechaResolucion' => date('Y-m-d H:i:s'),
                    'Exito' => 1,
                    'FlujoID' => $idFlujo,
                ]);

                $solicitudID = null;
                if ($solicitud) {
                    $Log->info("Solicitud creada con exito");

                    // Resuelve el arreglo de Log
                    $Log->solveArray($solicitud->id);

                    $solicitudID = $solicitud->id;

                    $notificacion = FLU_Notificaciones::Notificar($solicitudID, $idFlujo);
                    if ($notificacion) {
                        $Log->info("Notificacion creada con exito", $solicitudID);
                    }
                }

                // Logica de Reglas de Lead ---------------------------------------


                $reglaVendedor = $request->input('data.reglaVendedor') ?? true;
                if ($vendedorID == 1) $reglaVendedor = true;
                if ($reglaVendedor == true) $Log->info("Regla vendedor solicitada", $solicitudID);

                $reglaSucursal = $request->input('data.reglaSucursal') ?? false;
                if ($reglaSucursal == true) $Log->info("Regla sucursal solicitada", $solicitudID);

                if ($reglaVendedor == true || $reglaSucursal == true) {
                    $Log->info("Asignando reglas de Lead", $solicitudID);

                    // Se ajusta ejecución de regla sucursal. Siempre se debe ejecutar en conjunto
                    if ($reglaVendedor == true) {
//                    $reglaSucursal = true;
                        // Excepto para USADOS
                        if ($marcaNombre == "USADOS") {
                            $reglaSucursal = false;
                        }
                    }
                    $asignado = $this->reglaLead($lead,
                        $reglaVendedor,
                        $reglaSucursal,
                        $solicitudID,
                        $gerenciaHomologada
                    );
                    if ($asignado) {
                        $Log->info("Reglas de Lead ejecutadas con exito ", $solicitudID);
                        $vendedorID = $lead->VendedorID;
                        $sucursalID = $lead->SucursalID;
                    }

                }

                // Creacion de Agenda --------------------------

                if ($request->input('data.lead.agenda')) {
                    if ($cliente) {

                        $fechaInicio = Carbon::create($request->input('data.lead.agenda'));
                        $fechaFin = $fechaInicio->addHour();

                        $dataAgenda = [
                            "FechaCreacion" => Carbon::now()->format("Y-m-d H:i:s"),
                            "EventoCreacionID" => 12,
                            "UsuarioCreacionID" => 1,
                            "ClienteID" => $cliente->ID,
                            "ReferenciaID" => $lead->ID,
                            "TipoID" => 57,
                            "UsuarioID" => $vendedorID,
                            "EstadoID" => 1,
                            "Inicio" => $fechaInicio,
                            "Termino" => $fechaFin,
                            "Comentario" => "Agendamiento de Lead",
                            "Nombre" => ($request->input('data.nombre') ?? '') . ' ' . $request->input('data.apellido') ?? '',
                            "Rut" => $rut,
                            "Telefono" => $request->input('data.telefono') ?? ''
                        ];

                        $agenda = SIS_Agendamientos::create($dataAgenda);
                        if ($agenda) {
                            $Log->info("Agenda creada : " . $fechaInicio, $solicitudID);
                            $lead->Agendado = 1;
                            $lead->save();
                        } else {
                            $Log->error("Error al crear agenda", $solicitudID);
                        }
                    } else {
                        $Log->error("No se puede crear agenda, cliente no encontrado", $solicitudID);
                    }
                }

                // ---------------------------------------
            } else {
                Log::notice('Validacion de Lead incorrecta, no se procesa');
            }

        } catch (Exception $e) {

            $Log->error("Error al crear Lead: " . $e->getMessage());
            return response()->json(['status' => false, 'messages' => 'Ha ocurrido un error en la creacion de Lead'], 500);
        }

        return response()->json([
            'status' => true,
            'messages' => $returnMessage ?? "Ocurrio un error",
            'LeadID' => ($lead) ? $lead->ID : null,
        ], 200);
    }


    public static function reglaLead($lead, $reglaVendedor = false, $reglaSucursal = false, $solicitudID = null, $gerencia = 1)
    {
        // Logica de asignacion de Lead, con regla de Lead
        $Log = new Logger();

        $origen = $lead->OrigenID;
        $sucursalID = $lead->SucursalID;
        /*if($sucursalID){
            $Log->info("Buscando gerencia sucursal (SELECT GerenciaID FROM MA_Sucursales WHERE ID = $sucursalID)", $solicitudID);
            $gerenciaID = MA_Sucursales::where('ID', $sucursalID)->first()->GerenciaID;
        } else {
            $gerenciaID = $gerencia;
        }*/

        $gerenciaID = $gerencia;

        if ($reglaSucursal) {

            $Log->info("Buscando regla sucursal (SELECT FUNC_MK_ReglasLeadsGetSucursal ($gerenciaID, $origen) as sucursalID", $solicitudID);
            $sucursal = DB::select("SELECT FUNC_MK_ReglasLeadsGetSucursal (?, ?) as sucursalID", array($gerenciaID, $origen));

            if ($sucursal && $sucursal[0]->sucursalID != null && $sucursal[0]->sucursalID > 0) {
                $sucursalObj = MA_Sucursales::where('ID', $sucursal[0]->sucursalID)->first();
                $Log->info("Asignando sucursal a Lead : " . $sucursal[0]->sucursalID . " " . $sucursalObj->Sucursal, $solicitudID);

                $lead->SucursalID = $sucursal[0]->sucursalID;
                $sucursalID = $sucursal[0]->sucursalID; // Se actualiza sucursalID para asignacion de vendedor
                $lead->save();
            }
        }

        if ($reglaVendedor) {

            $Log->info("Buscando regla vendedor (SELECT FUNC_MK_ReglasLeadsGetVendedor ($sucursalID, $origen) as vendedorID", $solicitudID);
            $vendedor = DB::select("SELECT FUNC_MK_ReglasLeadsGetVendedor (?, ?) as vendedorID", array($sucursalID, $origen));

            if ($vendedor && $vendedor[0]->vendedorID != null && $vendedor[0]->vendedorID > 0) {
                $vendedorObj = MA_Usuarios::where('ID', $vendedor[0]->vendedorID)->first();
                $Log->info("Asignando vendedor a Lead : " . $vendedor[0]->vendedorID . " " . $vendedorObj->Nombre, $solicitudID);

                $lead->VendedorID = $vendedor[0]->vendedorID;
                $lead->save();
            }

        }


        return true;
    }


    public function leadHubspot(Request $request)
    {
        // Este metodo es para recibir Leads desde Hubspot, y crear un Lead en Roma
        // Se recibe el ID de Hubspot, y se crea un Lead en Roma con los datos del cliente
        // Se debe validar que el Lead no exista en Roma, si existe, no se crea
        // Si no existe, se crea un Lead con los datos del cliente y se guarda en Roma

        $Log = new Logger();
        $Log->info("Inicio creacion LEAD desde Hubspot");

        try {
            $lead = MK_Leads::where('IDHubspot', $request->input('data.lead.externalID'))->first();
            if ($lead) {
                $Log->info("Lead ya existe en BD Roma : " . $lead->ID);
                return response()->json(['status' => false, 'messages' => 'Lead ya existe'], 200);
            }

            DB::transaction(function () use ($request) {

                $client = new MA_Clientes();
                $client->FechaCreacion = date('Y-m-d H:i:s');
                $client->EventoCreacionID = 1;
                $client->UsuarioCreacionID = 1204; // Usuario de Integracion

                $client->Nombre = $request->input('data.nombre');
                $client->SegundoNombre = $request->input('data.segundoNombre') ?? '';
                if (!$client->SegundoNombre) {
                    $client->SegundoNombre = '';
                }
            });
        } catch (\Exception $e) {
            $Log->error("Error al crear Lead desde Hubspot: " . $e->getMessage());
            return response()->json(['status' => false, 'messages' => 'Ha ocurrido un error en la creacion de Lead'], 500);
        }

        return response()->json([
            'status' => true,
            'messages' => 'Lead creado correctamente desde Hubspot',
            'LeadID' => $lead->ID ?? null,
        ]);
    }

}


