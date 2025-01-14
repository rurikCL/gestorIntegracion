<?php

namespace App\Http\Controllers\ApiProd;

use App\Http\Controllers\Controller;
use App\Http\Resources\CC\CC_ReclamosCollection;
use App\Http\Resources\MA\MA_ClientesCollection;
use App\Http\Resources\MA\MA_ClientesResource;
use App\Http\Resources\VT\VT_VentasInfoCollection;
use App\Models\CC\CC_Reclamos;
use App\Models\Client;
use App\Models\MA\MA_Clientes;
use App\Models\MA\MA_ClientesTmp;
use App\Models\MK\MK_Leads;
use App\Models\SIS\SIS_Agendamientos;
use App\Models\SIS\SIS_Seguimientos;
use App\Models\SIS\SIS_Solicitudes;
use App\Models\VT\VT_ClientesDiarios;
use App\Models\VT\VT_Cotizaciones;
use App\Models\VT\VT_Renovaciones;
use App\Models\VT\VT_Ventas;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


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
class ClientesController extends Controller
{

    /**
     * Muestra los registros de Clientes
     * @OA\Get(
     *     path="/api/get/clientes",
     *     tags={"Clientes"},
     *     summary="Mostrar Clientes",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Mostrar todos los clientes."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function index()
    {
        return new MA_ClientesCollection(
            MA_Clientes::paginate()
        );
    }

    /**
     * Muestra el registro solicitado.
     * @param int $id
     * @return \Illuminate\Http\Response
     * @OA\Get(
     *     path="/api/lead/get/{cliente}",
     *     tags={"Cliente"},
     *     summary="Mostrar informacion de un Cliente",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         description="Parámetro necesario para la consulta de datos de un Cliente",
     *         in="path",
     *         name="cliente",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="int", value="1", summary="Introduce un rut de cliente.")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mostrar info de un cliente."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se ha encontrado el Cliente."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function show($rut)
    {
        return new MA_ClientesCollection(
            MA_Clientes::where('Rut', $rut)->get()
        );
    }


    /**
     * Registro de Cliente
     * @OA\Post(
     *     path="/api/put/cliente",
     *     tags={"Registrar Cliente"},
     *     summary="Registrar info de un Cliente",
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
     *                 example={"data" : { "nombre": "Pedro","segundoNombre": "Juan","apellido": "Perez","segundoApellido": "Muñoz","rut": "11111111","email": "contacto@email.com","telefono": "123456789","fechaNacimiento": "1980-01-01","direccion": "Alameda 1000"}}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cliente insertado"
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
            $client->Email = $request->input('data.email') ?? '';
            $client->Telefono = $request->input('data.telefono');
            $client->FechaNacimiento = $request->input('data.fechaNacimiento') ?? '';
            $client->Direccion = $request->input('data.direccion') ?? '';
            $client->save();
        });
        return response()->json(['messages' => 'Cliente creado correctamente'], 200);

    }

    public function update(Request $request)
    {

        try {
            $update = MA_Clientes::updateOrCreate(
                [
                    'Rut' => $request->input('data.rut')
                ], [
                    'Rut' => $request->input('data.rut'),
                    'FechaCreacion' => date('Y-m-d H:i:s'),
                    'EventoCreacionID' => 1,
                    'UsuarioCreacionID' => 1683,
                    'Nombre' => $request->input('data.nombre'),
                    'SegundoNombre' => $request->input('data.segundoNombre'),
                    'Apellido' => $request->input('data.apellido'),
                    'SegundoApellido' => $request->input('data.segundoApellido'),
                    'Email' => $request->input('data.email'),
                    'Telefono' => $request->input('data.telefono'),
                    'Direccion' => $request->input('data.direccion'),
                ]
            );
//            $update->save();
        } catch (Exception $e) {
            return response()->json(['status' => 0, 'messages' => 'Ha ocurrido un error al actualizar cliente'], 200);

        }

        return response()->json(['status' => 1, 'messages' => 'Cliente actualizado correctamente'], 200);

    }

    public function infoClienteVenta(Request $request)
    {
        if ($request->input('data.rut') != "" || $request->input('data.rut') != null) {
            $idCliente = MA_Clientes::where('Rut', $request->input('data.rut'))
                ->pluck('ID')->first();
        } else if ($request->input('data.id_cliente') > 0) {
            $idCliente = $request->input('data.id_cliente');
        } else {
            return [
                'status' => 1,
                'message' => "No se encontro el cliente"
            ];
        }

        $ventas = VT_Ventas::with('sucursal:GerenciaID,Sucursal', 'modelo:Modelo', 'cotizacionesTipoCredito:TipoCredito', 'tipoMantencion:Tipo', 'optiman:mantencion10_id,mantencion15_id,mantencion20_id,mantencion30_id,mantencion40_id,mantencion45_id,mantencion10_fecha,mantencion15_fecha,mantencion20_fecha,mantencion30_fecha,mantencion40_fecha,mantencion45_fecha,ID')
            ->where('ClienteID', $idCliente)
            ->get();

        return new VT_VentasInfoCollection($ventas);

    }

    public function infoReclamos(Request $request)
    {
        if ($request->input('data.rut') != "" || $request->input('data.rut') != null) {
            $idCliente = MA_Clientes::where('Rut', $request->input('data.rut'))
                ->pluck('ID')->first();
        } else if ($request->input('data.id_cliente')) {
            $idCliente = $request->input('data.id_cliente');
        } else {
            return [
                'status' => 1,
                'message' => "No se encontro el cliente"
            ];
        }

        return new CC_ReclamosCollection(
            CC_Reclamos::where('ClienteID', $idCliente)->get()
        );

    }


    public function revisarClientesDuplicados()
    {

        $duplicados = MA_Clientes::select('Rut', 'ID', DB::raw('count(*) as cantidad'))
            ->where('Rut', '<>', '')
            ->where('Rut', '<>', 0)
            ->groupBy('Rut')
            ->limit(500)
            ->havingRaw('count(*) > 1')
            ->get();

        foreach ($duplicados as $duplicado) {
            echo $duplicado->Rut;
            echo " (" . $duplicado->ID . ")";
            $casos = MA_Clientes::where('Rut', $duplicado->Rut)->get();
            $clientes = [];

            $primerCaso = $casos[0];
            $primerCasoID = $casos[0]->ID;

            foreach ($casos as $caso) {
                if ($caso->ID <> $primerCasoID) {

                    $nombre = $caso->Nombre;
                    $apellido = $caso->Apellido;
                    $email = $caso->Email;
                    $telefono = $caso->Telefono;
                    $rut = $caso->Rut;

                    $ventas = $caso->ventas->pluck('ID')->toArray();
                    $leads = $caso->leads->pluck('ID')->toArray();
                    $cotizaciones = $caso->cotizaciones->pluck('ID')->toArray();
                    $seguimientos = $caso->seguimientos->pluck('ID')->toArray();
                    $diarios = $caso->diarios->pluck('ID')->toArray();
                    $solicitudes = $caso->solicitudes->pluck('ID')->toArray();
                    $agendamientos = $caso->agendamientos->pluck('ID')->toArray();
                    $renovaciones = $caso->renovaciones->pluck('ID')->toArray();
                    $reclamos = $caso->reclamos->pluck('ID')->toArray();

                    VT_Ventas::whereIn('ID',$ventas)->update(['ClienteID' => $primerCasoID]);
                    MK_Leads::whereIn('ID',$leads)->update(['ClienteID' => $primerCasoID]);
                    VT_Cotizaciones::whereIn('ID',$cotizaciones)->update(['ClienteID' => $primerCasoID]);
                    SIS_Seguimientos::whereIn('ID',$seguimientos)->update(['ClienteID' => $primerCasoID]);
                    VT_ClientesDiarios::whereIn('ID', $diarios)->update(['ClienteID' => $primerCasoID]);
                    SIS_Solicitudes::whereIn('ID', $solicitudes)->update(['ClienteID' => $primerCasoID]);
                    SIS_Agendamientos::whereIn('ID', $agendamientos)->update(['ClienteID' => $primerCasoID]);
                    VT_Renovaciones::whereIn('ID', $renovaciones)->update(['ClienteID' => $primerCasoID]);
                    CC_Reclamos::whereIn('ID', $reclamos)->update(['ClienteID' => $primerCasoID]);

                    $clientes[] = [
                        'Nombre' => $nombre,
                        'Apellido' => $apellido,
                        'Email' => $email,
                        'Telefono' => $telefono,
                        'Rut' => $rut,
                        'ID' => $caso->ID,
                        'ventas' => $caso->ventas->pluck('ID')->toArray(),
                        'leads' => $caso->leads->pluck('ID')->toArray(),
                        'cotizaciones' => $caso->cotizaciones->pluck('ID')->toArray(),
                    ];

                    if ($nombre) {
                        $primerCaso->Nombre = $nombre;
                    }
                    if ($apellido) {
                        $primerCaso->Apellido = $apellido;
                    }
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $primerCaso->Email = $email;
                    }
                    if($telefono){
                        $primerCaso->Telefono = $telefono;
                    }

                    $primerCaso->save();

                    MA_ClientesTmp::create($caso->toArray());

                    $caso->delete();
                }

            }
            dump($clientes);

        }
    }

}
