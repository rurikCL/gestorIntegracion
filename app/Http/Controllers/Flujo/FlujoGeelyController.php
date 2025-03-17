<?php

namespace App\Http\Controllers\Flujo;

use App\Http\Controllers\Api\ApiSolicitudController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Controller;
use App\Models\Api\ApiSolicitudes;
use App\Models\FLU\FLU_Flujos;
use App\Models\FLU\FLU_Homologacion;
use App\Models\Lead;
use App\Models\MK\MK_Leads;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class FlujoGeelyController extends Controller
{
//    const ACCESS_KEY = 'e6387061534954323039';
//    const SECRET_KEY = 'a2a0c749-c27f-4c2f-a49e-9d9054cf8ab9';

    const ACCESS_KEY = '299454a350524F44323039';
    const SECRET_KEY = 'd09dff17-8045-4ae5-b773-e5efff3feafa';
    const HOST = 'openapi.geely.com';
    const LN = "\n";

    public function leadsGeely($numPage = 1)
    {
        echo "Ejecutando Flujo Geely : Paginacion $numPage <br>";
        Log::info("Inicio de flujo Geely");

        $flujo = FLU_Flujos::where('Nombre', 'Geely APIs')->first();

        if ($flujo->Activo) {
            $h = new FLU_Homologacion();

            echo ". . . <br>";

            $solicitudCon = new ApiSolicitudController();

            $referencia = $flujo->ID . date("ymdh");

            $req = new Request();
            $req['referencia_id'] = $referencia;
            $req['proveedor_id'] = 16;
            $req['api_id'] = 36;
            $req['prioridad'] = 1;
            $req['flujoID'] = $flujo->ID;
            $req['OnDemand'] = true;

            $req['data'] = [
                "appId" => self::ACCESS_KEY,
                "brandId" => "geely",
                "startingTime" => Carbon::now()->subDays(1)->getTimestampMs(),
//                "startingTime" => Carbon::createFromFormat("Y-m-d","2025-03-01")->getTimestampMs(),
                "endingTime" => Carbon::now()->getTimestampMs(),
                "pageNum" => $numPage,
                "pageSize" => 50
            ];

            $headers = [];
            $headers['X-Gapi-Ca-Timestamp'] = (int)(microtime(true) * 1000);
            $headers['X-Gapi-Ca-Algorithm'] = 'hmac-sha256';
            $headers['X-Gapi-Ca-Access-Key'] = self::ACCESS_KEY;
            $headers['X-Gapi-Ca-Signed-Headers'] = 'X-Gapi-Ca-Timestamp';
            $headers['Date'] = gmdate('D, d M Y H:i:s') . ' GMT';
            $headers['Host'] = self::HOST;
            $headers['X-Gapi-Ca-Signature'] = self::generateSignature('POST', '/lcms/router/rest/sale/lead/getLeadList', $headers, "");

            $req['dataHeader'] = $headers;

            dd($req->toArray());

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
//            dump($arrayData);


            // RECURSIVIDAD por paginacion
            if ($arrayData->data->current < $arrayData->data->pages) {
                $this->leadsGeely($numPage + 1);
            }


            // Recorre los registros
            if ($arrayData->data->records) {
                $leadObj = new LeadController();

                foreach ($arrayData->data->records as $record) {
                    $id = $record->id;
                    if (MK_Leads::where('IDExterno', $id)->exists()) {
                        print("Lead : " . $id . " ya existe... ");

                    } else {
                        print("Lead : " . $id . " no existe... creando");

                        $nombre = $record->firstName;
                        $rut = $record->rut;
                        $apellido = $record->lastName;
                        $email = $record->customerEmail;
                        $telefono = $record->customerPhone;
                        $modelo = $record->intentionModel;
                        $comuna = $record->cityName;
                        $sucursal = $record->followStoreCode;
                        $origenIngreso = $record->sourceCode;

                        $sucursal = $h->GetDato($sucursal,$flujo->ID,'sucursal', $sucursal);
                        $modelo = $h->GetDato($modelo,$flujo->ID,'modelo', $modelo);
                        $origenIngreso = $h->GetDato($origenIngreso,$flujo->ID,'origen', 1);

                        $req = new Request();
                        $req['data'] = [
                            "usuarioID" => 2904, // INTEGRACION HUBSPOT
                            "reglaVendedor" => 1,
                            "reglaSucursal" => 0,
                            "rut" => $rut,
                            "nombre" => $nombre,
                            "apellido" => $apellido,
                            "email" => $email,
                            "telefono" => $telefono,
                            "lead" => [
                                "idFlujo" => $flujo->ID,
                                "origenID" => 2,
                                "origenIngreso" => $origenIngreso,
                                "subOrigenID" => 63,
                                "marca" => "GEELY",
                                "modelo" => $modelo,
                                "sucursalID" => $sucursal,
                                "externalID" => $id,
                            ]
                        ];

                        $resultado = null;
                        $resultado = $leadObj->nuevoLead($req);
                        if ($resultado) {
                            $res = $resultado->getData();
                            Log::info("Lead Geely creado " . $res->LeadID);
//                            dump($res);
                        }
                    }
                }
            }
        }
    }

    function updateLead($idLead)
    {
        echo "Ejecutando Actualizacion Lead Geely <br>";
        Log::info("Actualizacion Lead Geely");

        $flujo = FLU_Flujos::where('Nombre', 'Geely APIs')->first();

        if ($flujo->Activo) {
            $h = new FLU_Homologacion();

            $solicitudCon = new ApiSolicitudController();

            $referencia = $flujo->ID . date("ymdh");
            $leadObj = MK_Leads::where('ID', $idLead)->first();
//            dump($leadObj);

            $req = new Request();
            $req['referencia_id'] = $referencia;
            $req['proveedor_id'] = 16;
            $req['api_id'] = 37;
            $req['prioridad'] = 1;
            $req['flujoID'] = $flujo->ID;
            $req['OnDemand'] = true;

            $req['data'] = [
                "appId" => self::ACCESS_KEY,
                "brandId" => "geely",
                "updateDealerLeadInfo" => [
                    "leadId" => $leadObj->IDExterno,
                    "followStatus" => $h->GetDato($leadObj->EstadoID,$flujo->ID,'leadStatus', 9),
                ]
            ];

            $headers = [];
            $headers['X-Gapi-Ca-Timestamp'] = (int)(microtime(true) * 1000);
            $headers['X-Gapi-Ca-Algorithm'] = 'hmac-sha256';
            $headers['X-Gapi-Ca-Access-Key'] = self::ACCESS_KEY;
            $headers['X-Gapi-Ca-Signed-Headers'] = 'X-Gapi-Ca-Timestamp';
            $headers['Date'] = gmdate('D, d M Y H:i:s') . ' GMT';
            $headers['Host'] = self::HOST;
            $headers['X-Gapi-Ca-Signature'] = self::generateSignature('POST', '/lcms/router/rest/sale/lead/updateLeadInfo', $headers, "");


            $req['dataHeader'] = $headers;

            dump($req->toArray());

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
            dump($arrayData);
        }

    }

    private static function generateSignature($method, $path, $headers, $queryString)
    {
        $contentArray = [];
        $contentArray[] = $method;
        $contentArray[] = $path;
        $contentArray[] = $queryString;
        if ($headers) {
            if ($headers['X-Gapi-Ca-Access-Key']) {
                $contentArray[] = $headers['X-Gapi-Ca-Access-Key'];
            }
            if ($headers['Date']) {
                $contentArray[] = $headers['Date'];
            }
            if ($headers['X-Gapi-Ca-Signed-Headers']) {
                $customHeaders = explode(';', $headers['X-Gapi-Ca-Signed-Headers']);
                foreach ($customHeaders as $header) {
                    $contentArray[] = $header . ':' . $headers[$header];
                }
            }
        }

        $content = implode(self::LN, $contentArray) . self::LN;
//        dump($content);
        $signature = base64_encode(hash_hmac('sha256', $content, self::SECRET_KEY, true));
        return $signature;
    }

    private static function buildQueryString($queries)
    {
        if (!$queries) {
            return "";
        }
        ksort($queries);
        $queryString = '';
        foreach ($queries as $k => $v) {
            if (is_array($v)) {
                asort($v);
                foreach ($v as $index => $value) {
                    $itemString = sprintf("%s=%s", $k, $value);
                    $queryString = $queryString . $itemString . '&';
                }
            } else {
                $itemString = sprintf("%s=%s", $k, $v);
                $queryString = $queryString . $itemString . '&';
            }
        }
        $queryString = trim($queryString, '&');
        return $queryString;
    }

    private static function buildUrl($path, $queries)
    {
        $queryString = self::buildQueryString($queries);
        $format_string = $queryString ? "%s%s?%s" : "%s%s";
        $url = sprintf("http://$format_string", self::HOST, $path, $queryString);
        return $url;
    }
}
