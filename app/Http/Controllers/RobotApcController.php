<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Api\ApiSolicitudController;
use App\Imports\ApcStockImport;
use App\Models\APC_Stock;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Saloon\XmlWrangler\XmlReader;


class RobotApcController extends Controller
{
    //

    private $client;
    private $cookie;
    private $cookieJar;

    public function __construct()
    {
        $this->setCookie();
        $this->client = new Client([
//            'cookies' => $this->cookieJar,
            'cookies' => true,
            'timeout' => 0,
//            'follow_location' => true,
            'follow_redirects' => true,
            'allow_redirects' => ['strict' => true],
//            'verify' => false,
        ]);
//        dd($this->client->getConfig("cookies"));
    }

    public function setCookie()
    {
        Log::info("definiendo cookie");
        $this->cookie = "cookiefileJar.txt";

        // si no existe el archivo, se crea
        /*        if(!file_exists($this->cookie)) {
                    $fh = fopen($this->cookie, "w");
                    fwrite($fh, "");
                    fclose($fh);
                }*/
        $this->cookieJar = new FileCookieJar($this->cookie, true);
    }

    public function get_site_html($site_url, $data = '', $header = [], $metodo = 'POST')
    {

        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $site_url,
            CURLOPT_COOKIEJAR => $this->cookie,
            CURLOPT_COOKIEFILE => $this->cookie,
//            CURLOPT_SSL_VERIFYPEER => false,
//            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => $header,
//            CURLOPT_VERBOSE => true,
        );

        if ($metodo == 'POST') {
            print("Modo POST ");
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_CUSTOMREQUEST] = 'POST';
            $options[CURLOPT_POSTFIELDS] = $data;
        } else {
            print("Modo GET ");
            $options[CURLOPT_CUSTOMREQUEST] = 'GET';
            if ($data != '') $options[CURLOPT_POSTFIELDS] = $data;
        }

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        if ($response === false) {
            echo 'Curl error: ' . curl_error($ch);
        }

        curl_close($ch);

        return $response;
    }

    public function traeStock()
    {

        set_time_limit(0);

        $this->setCookie();

        // Login
        $viewstate = $this->login();

        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        $filename = 'informeStock.xml';
        $filedata = Storage::get('public/viewstates/stockFull.json');

        $options['form_params'] = json_decode($filedata, true);
        $options['cookies'] = $this->cookieJar;
        $options['sink'] = storage_path('/app/public/' . $filename);

        if(file_exists(storage_path('/app/public/' . $filename))) {
            $res = true;
        }else{
            $request = new Request('POST', 'https://appspsa-cl.autoprocloud.com/vcl/Gestion/ShowDms_ConsultaStockTable.aspx', $headers);
            $res = $this->client->sendAsync($request, $options)->wait();
        }

        if ($res) {
            echo "Informe descargado, procesando... ";

            $filedata = Storage::read('/public/' . $filename);
            if ($filedata) {
                $xml = XmlReader::fromString(Storage::read('/public/' . $filename));
                $numCell = 0;
                $numCol = 0;

                APC_Stock::truncate();

                foreach ($xml->value('s:Row')->get() as $cell) {

                    $numCol = 0;
                    foreach ($cell['s:Cell'] as $data) {

                        if ($numCell > 0) {
                            $dataArray[$numCell][$headers[$numCol]] = $data['s:Data'];

                        } else {
                            $headers[$numCol] = Str::slug($data['s:Data'], '_');
                        }
                        $numCol++;
                    }

                    if ($numCell > 0) {
                        $row = $dataArray[$numCell];
                        APC_Stock::create([
                            'Empresa' => $row['empresa'],
                            'Sucursal' => $row['sucursal'],
                            'Folio_Venta' => $row['folio_venta'] ?? null,
                            'Venta' => ($row['venta'] != '') ? $row['venta'] : null,
                            'Estado_Venta' => $row['estado_venta'],
                            'Fecha_Venta' => ($row['fecha_venta'] != '') ? Carbon::createFromFormat("d-m-Y H:i:s", $row['fecha_venta'])->format('Y-m-d H:i:s') : null,
                            'Tipo_Documento' => $row['tipo_documento_folio'],
                            'Vendedor' => $row['vendedor'],
                            'Fecha_Ingreso' => ($row['fecha_ingreso'] != '') ? Carbon::createFromFormat("d-m-Y H:i:s", $row['fecha_ingreso'])->format('Y-m-d H:i:s') : null,
                            'Fecha_Facturacion' => ($row['fecha_facturacion'] != '') ? Carbon::createFromFormat("d-m-Y H:i:s", $row['fecha_facturacion'])->format('Y-m-d H:i:s') : null,
                            'VIN' => $row['numero_vin'],
                            'Marca' => $row['marca'],
                            'Modelo' => $row['modelo'],
                            'Version' => $row['version'],
                            'Codigo_Version' => $row['codigo_version'],
                            'Anio' => ($row['ano'] != '') ? $row['ano'] : null,
                            'Kilometraje' => $row['kilometraje'],
                            'Codigo_Interno' => $row['codigo_interno'],
                            'Placa_Patente' => $row['placa_patente'],
                            'Condicion_Vehículo' => $row['condicion_vehiculo'],
                            'Color_Exterior' => $row['color_exterior'],
                            'Color_Interior' => $row['color_interior'],
                            'Precio_Venta_Total' => ($row['precio_venta_total'] != '') ? $row['precio_venta_total'] : null,
                            'Estado_AutoPro' => $row['estado_autopro'],
                            'Dias_Stock' => ($row['dias_stock'] != '') ? $row['dias_stock'] : null,
                            'Estado_Dealer' => $row['estado_dealer'],
                            'Bodega' => $row['bodega'],
                            'Equipamiento' => $row['equipamiento'],
                            'Numero_Motor' => $row['numero_motor'],
                            'Numero_Chasis' => $row['numero_chasis'],
                            'Proveedor' => $row['proveedor'],
                            'Fecha_Disponibilidad' => ($row['fecha_disponibilidad'] != '') ? Carbon::createFromFormat("d-m-Y H:i:s", $row['fecha_disponibilidad'])->format('Y-m-d H:i:s') : null,
                            'Factura_Compra' => $row['factura_compra'],
                            'Vencimiento_Documento' => ($row['vencimiento_documento'] != '') ? Carbon::createFromFormat("d-m-Y H:i:s", $row['vencimiento_documento'])->format('Y-m-d H:i:s') : null,
                            'Fecha_Compra' => ($row['fecha_compra'] != '') ? Carbon::createFromFormat("d-m-Y H:i:s", $row['fecha_compra'])->format('Y-m-d H:i:s') : null,
                            'Fecha_Vencto_Rev_tec' => ($row['fecha_vencto_revision_tecnica'] != '') ? Carbon::createFromFormat("d-m-Y H:i:s", $row['fecha_vencto_revision_tecnica'])->format('Y-m-d H:i:s') : null,
                            'N_Propietarios' => $row['n_propietarios'],
                            'Folio_Retoma' => $row['folio_retoma'],
                            'Fecha_Retoma' => ($row['fecha_retoma'] != '') ? Carbon::createFromFormat("d-m-Y H:i:s", $row['fecha_retoma'])->format('Y-m-d H:i:s') : null,
                            'Dias_Reservado' => $row['dias_reservado'],
                            'Precio_Compra_Neto' => ($row['precio_compra_neto'] != '') ? $row['precio_compra_neto'] : null,
                            'Gasto' => $row['gasto'],
                            'Accesorios' => $row['accesorios'],
                            'Total_Costo' => ($row['total_costo'] != '') ? $row['total_costo'] : null,
                            'Precio_Lista' => ($row['precio_lista'] != '') ? $row['precio_lista'] : null,
                            'Margen' => ($row['margen'] != '') ? $row['margen'] : null,
//            'Margen_porcentaje' => $row[46],
                        ]);
                    }

                    $numCell++;
                }
            }
            unlink(storage_path('/app/public/' . $filename));

        }

//        Excel::import(new ApcStockImport(), $filename,  null, \Maatwebsite\Excel\Excel::XML);

    }

    public function login()
    {

        // login al sistema, genera las cookies con codigo de usuario
        $urlPrelogin = 'https://provider.autoprocloud.com/MC/home/mcHome.aspx/ValidaLogin';
        $headers = [
            'Content-Type' => 'application/json',
        ];
        /*        $headers = array(
                    'Content-Type: application/json',
                );*/
        $body = '{
            "userName": "rodrigo.larrain@pompeyo.cl",
            "Password":"Xt!5LN"
        }';
        $request = new Request('POST', $urlPrelogin, $headers, $body);
        $res = $this->client->sendAsync($request, ["cookies" => $this->cookieJar])->wait();
        $respuesta = $res->getBody();

//        $respuesta = json_decode($respuesta);
//        $respuesta = $this->get_site_html($urlPrelogin, $body, $headers);
        $respuesta = json_decode($respuesta);
        $userValidated = $respuesta->d->Message;

        // Segundo login, entrega pagina para viewstate
        $url = 'https://provider.autoprocloud.com/MC/home/mcHome.aspx/LogIn';
        $headers = [
            'Content-Type' => 'application/json',
        ];

        $body = '{
          "businessID": "205",
          "BranchID": "672",
          "ModuleID": "2",
          "username": "rodrigo.larrain@pompeyo.cl"
        }';

//        dd($this->client->getConfig("cookies"));

        $request = new Request('POST', $url, $headers, $body);
        $res = $this->client->sendAsync($request, ["cookies" => $this->cookieJar])->wait();
//        $respuesta = $this->get_site_html($url, $body, $headers);
        $respuesta = $res->getBody();
//        echo $respuesta;
//        dd($respuesta);
        $respuesta = json_decode($respuesta);
        if ($respuesta->d) {
            $viewstate = $this->getViewstate($respuesta->d);
        }

        return $viewstate;
    }

    public function getViewstate($url)
    {

//        $respuesta = $this->get_site_html('https://provider.autoprocloud.com/MC/home/mcHome.aspx/LogIn', '', [], 'GET');
        $request = new Request('GET', $url);
        $res = $this->client->sendAsync($request, ["cookies" => $this->cookieJar])->wait();
        $respuesta = $res->getBody();

        $matches = [];

        $busqueda = '/id="__VIEWSTATE" value="([^"]+)"/';
        preg_match($busqueda, $respuesta, $viewstate);
//        preg_match('~<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" />~',$respuesta,$viewstate);
//        dd($viewstate);
        $busqueda = '/id="__VIEWSTATEGENERATOR" value="([^"]+)"/';
        preg_match($busqueda, $respuesta, $generator);

        return [
            "viewstate" => $viewstate[1] ?? null,
            "generator" => $generator[1] ?? null,
        ];

    }

    public function excel()
    {
        $client = new Client();


    }

}
