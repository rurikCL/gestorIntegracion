<?php

namespace App\Http\Controllers\ApiProd;

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
use App\Models\FLU\FLU_Flujos;
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
use App\Models\SIS\SIS_Agendamientos;
use App\Models\TDP\TDP_FacebookSucursales;
use App\Models\TDP\TDP_WebPompeyoSucursales;
use Carbon\Carbon;
use HubSpot\Client\Crm\Deals\Model\AssociationSpec;
use HubSpot\Client\Crm\Deals\Model\PublicAssociationsForObject;
use HubSpot\Client\Crm\Deals\Model\PublicObjectId;
use HubSpot\Client\Crm\Deals\Model\SimplePublicObjectInputForCreate;
use HubSpot\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Exception;
use function Psl\Str\length;

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
class IncomingLeadsController extends Controller
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
//        return new LeadResource($lead);
    }


    /**
     * Regla de asignacion de Lead, con regla de Lead
     * @param $lead
     * @param bool $reglaVendedor
     * @param bool $reglaSucursal
     * @param null $solicitudID
     * @param int $gerencia
     * @return bool
     */

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
        $Log = new Logger();
        $Log->info("Recibiendo Lead Externo", $request->all());

        $flujo = FLU_Flujos::where('Nombre', 'Leads Hubspot')->first();
        $token = json_decode($flujo->Opciones);
        $client = Factory::createWithAccessToken($token->token);
        $h = new FLU_Homologacion();

        $rut = $request->input('data.datosCliente.rut', '');
        $email = $request->input('data.datosCliente.email', '');
        $telefono = $request->input('data.datosCliente.telefono', '');
        $nombre = $request->input('data.datosCliente.nombre', '');
        $apellido = $request->input('data.datosCliente.apellido', '');


        if ($rut) {
            $dv = substr($rut, -1);
            $rut = str_replace(".", "", substr($rut, 0, length($rut) - 1));
            $rutFormateado = $rut . "-" . $dv;
        } else {
            $rutFormateado = null;
        }
        $idContacto = 0;

// Creacion del CLIENTE (CONTACT)  -------------------------------------------

        // Busca cliente por email
        $filter = new \HubSpot\Client\Crm\Contacts\Model\Filter();

        if ($rut != '') {
            $filter->setOperator('EQ')
                ->setPropertyName('rut')
                ->setValue($rutFormateado);
            $Log->info("Buscando por Rut : " . $rutFormateado);
        } else if ($email != '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $filter->setOperator('EQ')
                ->setPropertyName('email')
                ->setValue($email);
            $Log->info("Buscando por Email : " . $email);
        } else {
            $filter->setOperator('EQ')
                ->setPropertyName('phone')
                ->setValue($telefono);
            $Log->info("Buscando por Telefono : " . $telefono);
        }


        $filterGroup = new \HubSpot\Client\Crm\Contacts\Model\FilterGroup();
        $filterGroup->setFilters([$filter]);

        $searchRequest = new \HubSpot\Client\Crm\Contacts\Model\PublicObjectSearchRequest();
        $searchRequest->setFilterGroups([$filterGroup]);

        $searchRequest->setProperties(['hs_object_id', 'firstname', 'lastname', 'email', 'rut']);

//            print_r("Datos contacto : $email, $nombre, $apellido, $telefono, $rut");
        $contacto = $client->crm()->contacts()->searchApi()->doSearch($searchRequest)->getResults();
//            print_r($contacto);

        if ($contacto) {
            foreach ($contacto as $item) {
                $data = $item->jsonSerialize();
                $idContacto = $data->id;
                print_r("contacto encontrado : " . $data->id);
                break;
            }

        } else {
            print_r("Contacto no encontrado");
        }

        if ($idContacto == 0) {
            try {
                $contactInput = new \HubSpot\Client\Crm\Contacts\Model\SimplePublicObjectInputForCreate();
                $dataContacto = [
                    'email' => filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null,
                    'firstname' => $nombre,
                    'lastname' => $apellido,
                    'phone' => $telefono,
                    'rut' => $rutFormateado,
                    'hs_marketable_status' => 2,  // 1: Marketing contact, 2: Non-marketing contact
                ];

                $contactInput->setProperties($dataContacto);
                $contact = $client->crm()->contacts()->basicApi()->create($contactInput);
                $idContacto = $contact->getId();
                print_r("Contacto creado : " . $idContacto);

            } catch (\Exception $e) {
                $respuesta = $e->getMessage();
                echo $respuesta;
                $regex = "/Existing ID: (\d*)\"/m";
                $posibleID = '';

                if (preg_match($regex, $respuesta, $posibleID)) {
                    print_r("Contacto existente: " . $posibleID[1]);
                    $idContacto = $posibleID[1];
                }

                $regex = "/Property values were not valid/m";
                if (preg_match($regex, $respuesta)) {
                    $Log->error("Error al crear contacto: " . $respuesta, $request->all());
                }
            }

        }


        // Creacion del NEGOCIO (DEAL)  -------------------------------------------

        $IDExterno = $request->input('data.lead.externalID', null);
        $idFlujoHomologacion = $request->input('data.lead.idFlujo', null);
        $sucursalNombre = $request->input('data.lead.sucursal', null);
        $sucursalIDExterno = $request->input('data.lead.sucursalExternalID', null);
        $comentario = $request->input('data.lead.comentario', null);

        $h->setFlujo($idFlujoHomologacion);

        $estadoHomologado = "appointmentscheduled"; // Estado homologado para Hubspot

        $associationSpec1 = new AssociationSpec([
            'association_category' => 'HUBSPOT_DEFINED',
            'association_type_id' => 3
        ]);
        $to1 = new PublicObjectId([
            'id' => $idContacto
        ]);
        $publicAssociationsForObject1 = new PublicAssociationsForObject([
            'types' => [$associationSpec1],
            'to' => $to1
        ]);

        $marcaNombre = $request->input('data.vehiculo.marca', null);
        $marcaIDExterno = $request->input('data.vehiculo.marcaExternalID', null);
        if($marcaIDExterno){
            $marcaHomologadaID = $h->getD('marca', $marcaIDExterno, 0);
        }
        $modeloNombre = $request->input('data.vehiculo.modelo', null);
        $modeloIDExterno = $request->input('data.vehiculo.modeloExternalID', null);
        if($modeloIDExterno){
            $modeloHomologadoID = $h->getD('modelo', $modeloIDExterno, 0);
        }
        $versionNombre = $request->input('data.vehiculo.version', null);
        $versionIDExterno = $request->input('data.vehiculo.versionExternalID', null);
        if($versionIDExterno){
            $versionHomologadoID = $h->getD('version', $versionIDExterno, 0);
        }
        $precioVehiculo = $request->input('data.vehiculo.precioVehiculo', null);
        $bonoMarca = $request->input('data.vehiculo.bonoMarca', null);
        $bonoFinanciamiento = $request->input('data.vehiculo.bonoFinanciamiento', null);


        $properties1 = [
            'id_externo' => $IDExterno,
            'record_id___contacto' => $idContacto,
            'email' => $email,
            'phone' => $telefono,
            'rut' => $rutFormateado,
            'firstname' => $nombre,
            'lastname' => $apellido,
            'dealname' => $nombre . ' ' . $apellido . ' - ' . $marcaNombre . ' ' . $modeloNombre, // + marca + modelo
            'idvendedor' => null,
            'nombrevendedor' => $lead->vendedor->Nombre ?? null,
            'sucursal' => $lead->sucursal->Sucursal ?? null,
            'origen_roma' => 2, //origen Marca
            'suborigen_roma' => 63, //suborigen Marca
            'canal_roma' => 2, //canal Digital
            'modelo' => $modeloNombre,
            'modelo_roma' => $modeloHomologadoID ?? null,
            'marca' => $marcaNombre,
            'marca_roma' => $marcaHomologadaID ?? null,
            'version' => $versionNombre,
            'version_roma' => $versionHomologadoID ?? null,
            'dealstage' => $estadoHomologado,
            'createdate' => Carbon::parse($lead->FechaCreacion)->format('Y-m-d'),
            'tipo_vehiculo' => 'Nuevo',
            'precio_vehiculo' => $precioVehiculo,
            'bono_marca' => $bonoMarca,
            'bono_financiamiento' => $bonoFinanciamiento,
            'vpp' => $request->input('data.vpp.tieneVpp', null),
            'financiamiento' => $request->input('data.financiamiento.conFinanciamiento', null),

        ];
//            print_r($properties1);
//            dd($properties1);

        try {
            $simplePublicObjectInputForCreate = new SimplePublicObjectInputForCreate([
                'associations' => [$publicAssociationsForObject1],
                'object_write_trace_id' => 'string',
                'properties' => $properties1,
            ]);

            $apiResponse = $client->crm()->deals()->basicApi()->create($simplePublicObjectInputForCreate);
            $idNegocio = $apiResponse->getId();
            print_r("<br>Negocio Creado : " . $idNegocio . "<br>");

            $Log->info('Lead ' . $lead->ID . ' sincronizado con exito');

        } catch (\Exception $e) {
            echo "Exception when calling basic_api->create: ", $e->getMessage();
        }

        return response()->json(['message' => 'Lead creado exitosamente', 'idNegocio' => $idNegocio], 201);
    }

}


