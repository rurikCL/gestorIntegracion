<?php

namespace App\Http\Controllers\Flujo;

use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Controller;
use App\Models\FLU\FLU_Flujos;
use App\Models\MA\MA_SubOrigenes;
use App\Models\MK\MK_Leads;
use HubSpot\Client\Crm\Contacts\ApiException;
use HubSpot\Client\Crm\Contacts\Model\Filter;
use HubSpot\Client\Crm\Deals\Model\Filter as FilterDeal;
use HubSpot\Client\Crm\Deals\Model\FilterGroup;
use HubSpot\Client\Crm\Deals\Model\PublicObjectSearchRequest;
use HubSpot\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FlujoHubspotController extends Controller
{
    public function leadsHubspotDeals()
    {
        echo "Ejecutando Flujo Hubspot <br>";
        Log::info("Inicio de flujo Hubspot");

        $flujo = FLU_Flujos::where('Nombre', 'Leads Hubspot')->first();

        if ($flujo->Activo) {

            $token = json_decode($flujo->Opciones);
            $client = Factory::createWithAccessToken($token->token);

            $filter1 = new FilterDeal([
                'property_name' => 'idpompeyo',
                'operator' => 'NOT_HAS_PROPERTY'
            ]);

            $filter2 = new FilterDeal([
                'property_name' => 'origen',
                'operator' => 'EQ',
                'value' => 'RELIF'
            ]);
            $filterGroup1 = new FilterGroup([
                'filters' => [$filter1, $filter2]
            ]);
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

                        $newProperties->setProperties([
                            'idpompeyo' => $lead->ID,
                            'idvendedor' => $lead->VendedorID,
                            'nombrevendedor' => $lead->vendedor->Nombre,
                        ]);
                        $client->crm()->deals()->basicApi()->update($data->id, $newProperties);

                    } else {
                        print("Lead no encontrado <br>");
                        Log::info("Creando nuevo Lead");

                        if ($data->properties['email'] == '' && $data->properties['phone'] == ''
                            && $data->properties['nombre'] == '' && $data->properties['rut'] == '') {
                            print("No hay datos de cliente, se busca contacto <br>");

                            if ($data->properties['record_id___contacto'] ?? '' != '') {
                                $dataContacto = $this->getContactInfo($data->properties['record_id___contacto'], $token->token);
                                $nombre = $dataContacto['nombre'] ?? '';
                                $email = $dataContacto['email'] ?? '';
                                $telefono = $dataContacto['telefono'] ?? '';
                                $rut = $dataContacto['rut'] ?? '';
                            }

                        } else {
                            $nombre = $data->properties['firstname'] . ' ' . $data->properties['lastname'] ?? '';
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

                        $reglaSucursal = $data->properties['reglasucursal'] ?? 1;
                        $reglaVendedor = $data->properties['reglavendedor'] ?? 1;

                        if($marca == 'USADOS'){
                            $sucursal = 'USADOS BILBAO';
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

                        $comentario = ($vpp) ? ' *Tiene VPP ' : '';

                        // Revision de fuente ----------------------------------
                        $origen = 8;
                        $subOrigen = 36;

                        $origenData = MA_SubOrigenes::Alias($fuente)->first();
                        if ($origenData) {
                            $subOrigen = $origenData->ID;
                            $origen = $origenData->OrigenID;
                        }

//                            $reglaVendedor = false;
//                            $reglaSucursal = false;

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
                    "nombre" => $data->properties['firstname'] . ' ' . $data->properties['lastname'] ?? '',
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
}
