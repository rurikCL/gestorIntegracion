<?php

namespace App\Http\Controllers\Flujo;

use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Controller;
use App\Models\FLU\FLU_Flujos;
use App\Models\FLU\FLU_Homologacion;
use App\Models\MA\MA_Clientes;
use App\Models\MA\MA_SubOrigenes;
use App\Models\MK\MK_Leads;
use Carbon\Carbon;
use HubSpot\Client\Crm\Contacts\ApiException;
use HubSpot\Client\Crm\Contacts\Model\Filter;
use HubSpot\Client\Crm\Deals\Model\Filter as FilterDeal;
use HubSpot\Client\Crm\Deals\Model\FilterGroup;
use HubSpot\Client\Crm\Deals\Model\PublicObjectSearchRequest;
use HubSpot\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Exception;

class FlujoHubspotController extends Controller
{
    public function leadsHubspotDeals()
    {
        echo "Ejecutando Flujo Hubspot Negocios <br>";
        Log::info("Inicio de flujo Hubspot");

        $flujo = FLU_Flujos::where('Nombre', 'Leads Hubspot')->first();

        if ($flujo->Activo) {

            $token = json_decode($flujo->Opciones);
            $client = Factory::createWithAccessToken($token->token);


            // FILTROS   -----------------------------------------------------
            $filter1 = new FilterDeal([
                'property_name' => 'idpompeyo',
                'operator' => 'NOT_HAS_PROPERTY'
            ]);

            $filter2 = new FilterDeal([
                'property_name' => 'origen',
                'operator' => 'HAS_PROPERTY',
            ]);

            $filter3 = new FilterDeal([
                'property_name' => 'hs_analytics_source_data_2',
                'operator' => 'NEQ',
                'value' => 'tailored leads flotas campaña'
            ]);

            $filter4 = new FilterDeal([
                'property_name' => 'preparado',
                'operator' => 'EQ',
                'value' => '1'
            ]);

            $filterGroup1 = new FilterGroup([
                'filters' => [$filter1, $filter2, $filter4]
            ]);
            // --------------------------------------------------------------

            $publicObjectSearchRequest = new PublicObjectSearchRequest([
                'properties' => ['idpompeyo', 'record_id___contacto', 'comentario', 'email', 'financiamiento', 'marca', 'modelo', 'nombre', 'origen', 'phone', 'rut', 'sucursal', 'reglasucursal', 'reglavendedor', 'usados', 'vpp', 'link_conversacion', 'agenda_visita', 'firstname', 'lastname'],
                'filter_groups' => [$filterGroup1],
                'limit' => $flujo->MaxLote,
            ]);


            try {

                $apiResponse = $client->crm()->deals()->searchApi()
                    ->doSearch($publicObjectSearchRequest)
                    ->getResults();

//                Log::info("Leads a procesar : " . count($apiResponse));
                foreach ($apiResponse as $item) {
                    $data = $item->jsonSerialize();

                    print("Buscando Lead : " . $data->id . "<br>");
                    $lead = MK_Leads::where('IDExterno', $data->id)->first();

                    $newProperties = new \HubSpot\Client\Crm\Deals\Model\SimplePublicObjectInput();

                    if ($lead) {
                        print("Lead encontrado : " . $lead->ID . "<br>");
                        Log::info("Lead encontrado : " . $lead->ID) . " - " . $lead->IDExterno;

                        $newProperties->setProperties([
                            'idpompeyo' => $lead->ID,
                            'idvendedor' => $lead->VendedorID,
                            'nombrevendedor' => $lead->vendedor->Nombre,
                        ]);
                        $client->crm()->deals()->basicApi()->update($data->id, $newProperties);

                    } else {
                        print("Lead no encontrado <br>");
                        Log::info("Creando nuevo Lead");

                        // Si no trae data de contacto
                        if ($data->properties['email'] == '' && $data->properties['phone'] == ''
                            && $data->properties['firstname'] == '' && $data->properties['rut'] == '') {

                            Log::info("No hay datos de cliente, se busca contacto <br>");

                            // Si trae id de contacto, trae la data desde el contacto
                            if (isset($data->properties['record_id___contacto'])) {
                                $dataContacto = $this->getContactInfo($data->properties['record_id___contacto'], $token->token);
                                $nombre = $dataContacto['nombre'] ?? '';
                                $email = $dataContacto['email'] ?? '';
                                $telefono = $dataContacto['telefono'] ?? '';
                                $rut = $dataContacto['rut'] ?? '';
                            } else {
                                // Si no se obtuvo data de contacto, se ignora el registro
                                continue;
                            }

                        } else {
                            $nombre = (($data->properties['firstname'] ?? '') . ' ' . $data->properties['lastname']);
//                            $nombre = ($data->properties['firstname'] ?? '');
                            $apellido = ($data->properties['lastname'] ?? '');
                            $email = $data->properties['email'] ?? '';
                            $telefono = $data->properties['phone'] ?? '';
                            $rut = $data->properties['rut'] ?? '';
                        }

                        $marca = $data->properties['marca'] ?? '';
                        $modelo = $data->properties['modelo'] ?? '';
                        $fuente = $data->properties['hs_analytics_source_data_1'] ?? '';

                        $origenProp = $data->properties['origen'] ?? '';
                        $idExterno = $data->id ?? '';

                        $vpp = $data->properties['vpp'] ?? 0;
                        if ($vpp === 'SI') {
                            $vpp = 1;
                        } else {
                            $vpp = 0;
                        }

                        if ($data->properties['link_conversacion']) {
                            $linkConversacion = $data->properties['link_conversacion'];
                        } else {
                            $linkConversacion = '';
                        }

                        if ($data->properties['agenda_visita']) {
                            Log::info("Agenda Visita : " . $data->properties['agenda_visita']);
                            $agendaVisita = Carbon::parse($data->properties['agenda_visita'])
                                ->startOfHour()
                                ->format('Y-m-d H:i:s')
                                ?? null;
                        } else {
                            $agendaVisita = null;
                        }

                        $reglaSucursal = $data->properties['reglasucursal'] ?? 1;
                        $reglaVendedor = $data->properties['reglavendedor'] ?? 1;

                        if ($marca == 'USADOS') {
                            $sucursal = 'USADOS BILBAO';
                            $reglaSucursal = 0;
                        } else {
                            $sucursal = $data->properties['sucursal'] ?? '';
                        }
                        $canal = $data->properties['canal'] ?? '';

                        $financiamiento = $data->properties['financiamiento'] ?? 0;
                        if ($financiamiento === 'SI') {
                            $financiamiento = 1;
                        } else {
                            $financiamiento = 0;
                        }

                        $comentario = $data->properties['comentario'] ?? '';
                        $comentario .= ($vpp) ? ' *Tiene VPP ' : '';

                        // Revision de fuente ----------------------------------
                        $origen = 8;
                        $subOrigen = 36;

                        $origenData = MA_SubOrigenes::Alias($origenProp)->first();
                        if ($origenData) {
                            $subOrigen = $origenData->ID;
                            $origen = $origenData->OrigenID;
                        } else {
                            Log::info("SubOrigen no encontrado : " . $origenProp);
                        }


                        // --------------------------------------------------------


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
                                "link" => $linkConversacion,
                                "agenda" => $agendaVisita,
                            ]
                        ];

                        $resultado = null;
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
                                $client->crm()->deals()->basicApi()->update($data->id, $newProperties);
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

    public function limpiarLeads()
    {
        echo "Ejecutando Flujo Hubspot Limpieza <br>";
        Log::info("Inicio de flujo Limpieza");

        $flujo = FLU_Flujos::where('Nombre', 'Leads Hubspot')->first();

        if ($flujo->Activo) {

            $token = json_decode($flujo->Opciones);
            $client = Factory::createWithAccessToken($token->token);


            // FILTROS   -----------------------------------------------------
            $filter1 = new FilterDeal([
                'property_name' => 'idpompeyo',
                'operator' => 'HAS_PROPERTY'
            ]);

            $filter2 = new FilterDeal([
                'property_name' => 'origen',
                'operator' => 'NEQ',
                'value' => 'RELIF'
            ]);

            $filter3 = new FilterDeal([
                'property_name' => 'hs_analytics_source_data_2',
                'operator' => 'NEQ',
                'value' => 'tailored leads flotas campaña'
            ]);

            $filterGroup1 = new FilterGroup([
                'filters' => [$filter1, $filter2, $filter3]
            ]);
            // --------------------------------------------------------------

            $publicObjectSearchRequest = new PublicObjectSearchRequest([
                'properties' => ['idpompeyo', 'record_id___contacto', 'comentario', 'email', 'financiamiento', 'marca', 'modelo', 'nombre', 'origen', 'phone', 'rut', 'sucursal', 'reglavendedor', 'usados', 'vpp'],
                'filter_groups' => [$filterGroup1],
                'limit' => $flujo->MaxLote,
            ]);


            try {

                $apiResponse = $client->crm()->deals()->searchApi()
                    ->doSearch($publicObjectSearchRequest)
                    ->getResults();

//                Log::info("Leads a procesar : " . count($apiResponse));
                foreach ($apiResponse as $item) {
                    $data = $item->jsonSerialize();

                    print("Buscando Lead : " . $data->id . "<br>");
                    $lead = MK_Leads::where('IDExterno', $data->id)->first();

                    $newProperties = new \HubSpot\Client\Crm\Deals\Model\SimplePublicObjectInput();

                    if ($lead) {
                        print("Lead encontrado : " . $lead->ID . "<br>");
                        Log::info("Lead encontrado : " . $lead->ID) . " - " . $lead->IDExterno;
                        $lead->FechaCreacion = $item->getProperties()['createdate'];

                        $newProperties->setProperties([
                            'idpompeyo' => $lead->ID,
                            'idvendedor' => $lead->VendedorID,
                            'nombrevendedor' => $lead->vendedor->Nombre,
                        ]);
                        $client->crm()->deals()->basicApi()->update($data->id, $newProperties);

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

    public function actualizaLeadHubspot()
    {
        echo "Ejecutando Flujo Hubspot Actualizacion <br>";
        Log::info("Inicio de flujo Actualizacion Deals Hubspot (etapa / estado)");

        $flujo = FLU_Flujos::where('Nombre', 'Leads Hubspot')->first();

        if ($flujo->Activo) {

            $token = json_decode($flujo->Opciones);
            $client = Factory::createWithAccessToken($token->token);
            $h = new FLU_Homologacion();

            $leads = MK_Leads::where('LogEstado', 1)
                ->where('FechaCreacion', '>=', '2024-04-01 00:00:00')
                ->where('IDExterno', '>', 0)
                ->where('OrigenID', 8)
                ->get();

            if ($leads->count()) {
                Log::info("leads encontrados " . $leads->count());
                foreach ($leads as $lead) {
                    Log::info("Lead a actualizar : " . $lead->ID . " - " . $lead->IDExterno);

                    $newProperties = new \HubSpot\Client\Crm\Deals\Model\SimplePublicObjectInput();
                    $estadoHomologado = $h->getDato($lead->estadoLead->Estado, $flujo->ID, 'estado', false);

                    if ($estadoHomologado) {
                        $newProperties->setProperties([
                            'dealstage' => $estadoHomologado
                        ]);

                        try {
                            $res = $client->crm()->deals()->basicApi()->update($lead->IDExterno, $newProperties);

                            $lead->LogEstado = 0;
                            $lead->save();

                        } catch (\Exception $e) {
                            Log::error("Error al actualizar deal hubspot " . $lead->IDExterno);
                            $lead->LogEstado = 2;
                            $lead->save();
                        }
                    }

                }
            } else {
                Log::info("No hay leads para actualizar");
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

                        $comentario .= ($vpp) ? ' *Tiene VPP ' : '';

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

    // esta funcion es para obtener la informacion de un contacto en hubspot
    public function getContactInfo($id, $token)
    {

        $client = Factory::createWithAccessToken($token);
        $returnData = [];

        try {
            $apiResponse = $client->crm()->contacts()->basicApi()->getById($id,
                ['firstname,lastname,phone,email,rut, marca,modelo,hs_analytics_source_data_1,compra_con_financiamiento,reglasucursal,reglavendedor,canal,vpp,financiamiento,sucursal,idpompeyo,origen']
            );
            if ($apiResponse) {
                $data = $apiResponse->jsonSerialize();
                $returnData = [
                    "nombre" => ($data->properties['firstname'] ?? '') . ' ' . ($data->properties['lastname'] ?? ''),
                    "email" => $data->properties['email'] ?? '',
                    "telefono" => $data->properties['phone'] ?? '',
                    "rut" => $data->properties['rut'] ?? '',
                ];
            }
        } catch (ApiException $e) {
            echo "Exception when calling basic_api->get_by_id: ", $e->getMessage();
        }

        return $returnData;
    }

    public function revisaLeadsHubspot()
    {

        $leads = MK_Leads::where('IDExterno', '<>', '')
            ->where('FechaCreacion', '>=', '2024-07-04 00:00:00')
            ->get();
        $flujo = FLU_Flujos::where('Nombre', 'Leads Hubspot')->first();
        $token = json_decode($flujo->Opciones);
        $client = Factory::createWithAccessToken($token->token);

        foreach ($leads as $lead) {
            print_r("revisando lead : " . $lead->IDExterno . "<br>");

            try {
                $apiResponse = $client->crm()->deals()->basicApi()->getById($lead->IDExterno, ['idpompeyo', 'record_id___contacto', 'comentario', 'email', 'financiamiento', 'marca', 'modelo', 'nombre', 'origen', 'phone', 'rut', 'sucursal', 'reglasucursal', 'reglavendedor', 'usados', 'vpp', 'link_conversacion', 'agenda_visita', 'firstname', 'lastname']);
                if ($apiResponse) {
                    $data = $apiResponse->jsonSerialize();

                    $nombre = ($data->properties['firstname'] ?? '');
                    $apellido = ($data->properties['lastname'] ?? '');
                    $email = $data->properties['email'] ?? '';
                    $telefono = $data->properties['phone'] ?? '';
                    $rut = $data->properties['rut'] ?? '';

                    if($rut) {

                        $rut = substr($rut, 0, 15);
                        $rut = str_replace('.', '', $rut);
                        $rut = str_replace('-', '', $rut);
                        $rut = str_replace(' ', '', $rut);

                        Log::info("Buscando Rut : " . $rut);
                        $cliente = MA_Clientes::where('Rut', $rut)->first();

                        if ($cliente) {
                            Log::info("Cliente encontrado : " . $rut);
                            $idCliente = $cliente->ID;
                        } else {
                            Log::info("Cliente no encontrado se crea uno nuevo: ");
                            $cliente = new MA_Clientes();

                            $cliente->Rut = $rut;
                            $cliente->FechaCreacion = date('Y-m-d H:i:s');
                            $cliente->EventoCreacionID = 1;
                            $cliente->UsuarioCreacionID = 3; // 2824
                            $cliente->Nombre = $nombre;
                            $cliente->Apellido = $apellido;
                            $cliente->Email = $email;
                            $cliente->Telefono = $telefono;
                            $cliente->FechaNacimiento = null;

                            $cliente->save();

                            Log::notice("Cliente creado: " . $cliente->ID);
                            $idCliente = $cliente->ID;
                        }

                        if ($idCliente != $lead->CLienteID) {
                            $lead->ClienteID = $idCliente;
                            $lead->save();
                            Log::info("Lead cliente actualizado : " . $lead->ID . ":" . $idCliente);
                        } else {
                            Log::info("Lead correcto");
                        }

//                        dd($data);
                    } else {
                        Log::info("Lead hubspot no posee rut");
                    }
                }
            } catch (ApiException $e) {
                echo "Exception when calling basic_api->get_by_id: ", $e->getMessage();
            }
        }


    }
}
