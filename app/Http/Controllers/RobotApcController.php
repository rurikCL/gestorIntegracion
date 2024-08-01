<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Api\ApiSolicitudController;
use DOMDocument;
use DOMXPath;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;


class RobotApcController extends Controller
{
    //

    private $client;
    private $cookie;

    public function __construct()
    {
        $this->client = new Client([
            'cookies' => true,
            'follow_redirects' => true,
//            'verify' => false,
        ]);
    }

    public function __invoke()
    {
        $this->setCookie();
    }

    public function setCookie()
    {
        Log::info("definiendo cookie");
        $this->cookie = "cookiefile.txt";
        if(!file_exists($this->cookie)) {
            $fh = fopen($this->cookie, "w");
            fwrite($fh, "");
            fclose($fh);
        }
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

        if($metodo == 'POST'){
            print("Modo POST ");
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_CUSTOMREQUEST] = 'POST';
            $options[CURLOPT_POSTFIELDS] = $data;
        } else {
            print("Modo GET ");
            $options[CURLOPT_CUSTOMREQUEST] = 'GET';
            if($data != '') $options[CURLOPT_POSTFIELDS] = $data;
        }

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        if ($response === false) {
            echo 'Curl error: ' . curl_error($ch);
        }

        curl_close($ch);

        return $response;
    }
    public function traeStock(){

        // Login
        $viewstate = $this->login();

        $url = 'https://appspsa-cl.autoprocloud.com/vcl/Gestion/ShowDms_ConsultaStockTable.aspx';
//        $robot = new RobotController();

        Log::info("Inicio Scraping Stock" );

        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: 0',
        );

        $options = [
//            'form_params' => [
                'ctl00$scriptManager1' => 'ctl00$PageContent$UpdatePanel1|ctl00$PageContent$Dms_ConsultaStockVehiculoViewPagination$_PageSizeButton',
                'ctl00$pageLeftCoordinate' => '',
                'ctl00$pageTopCoordinate' => '',
                'ctl00$_mc_Header$_clientSideIsPostBack' => 'N',
                'ctl00$_mc_Header$_mcMenuSuperior$_clientSideIsPostBack' => '',
                'ctl00$PageContent$_clientSideIsPostBack' => '',
                'ctl00$PageContent$Dms_ConsultaStockVehiculoViewPanelExtender_ClientState' => 'false',
                'ctl00$PageContent$id_EmpresaFilter' => '205',
                'ctl00$PageContent$id_SucursalFilter4' => '617',
                'ctl00$PageContent$id_BodegaFilter4' => '--PLEASE_SELECT--',
                'ctl00$PageContent$id_ProveedorFilter' => '',
                'ctl00$PageContent$RBCompra' => 'FechaCompraRB',
                'ctl00$PageContent$Fecha_CompraFromFilter' => '',
                'ctl00$PageContent$Fecha_CompraToFilter' => '',
                'ctl00$PageContent$RBVenta' => 'Fecha_VentaRb',
                'ctl00$PageContent$Fecha_FacturacionFromFilter' => '',
                'ctl00$PageContent$Fecha_FacturacionToFilter' => '',
                'ctl00$PageContent$PagadoFilter' => '--PLEASE_SELECT--',
                'ctl00$PageContent$id_Estado_UnidadFilter' => '--PLEASE_SELECT--',
                'ctl00$PageContent$Placa_PatenteFilter' => '',
                'ctl00$PageContent$Codigo_InternoFilter4' => '',
                'ctl00$PageContent$Numero_ChasisFilter' => '',
                'ctl00$PageContent$Numero_MotorFilter' => '',
                'ctl00$PageContent$Numero_VINFilter' => '',
                'ctl00$PageContent$id_MarcaFilter4' => '--PLEASE_SELECT--',
                'ctl00$PageContent$id_Familia_ModeloFilter4' => '--PLEASE_SELECT--',
                'ctl00$PageContent$id_ModeloFilter4' => '--PLEASE_SELECT--',
                'ctl00$PageContent$Codigo_InternoFilter' => '',
                'ctl00$PageContent$AñoFromFilter4' => '',
                'ctl00$PageContent$AñoToFilter4' => '',
                'ctl00$PageContent$id_Color_ExteriorFilter4' => '--PLEASE_SELECT--',
                'ctl00$PageContent$id_Tipo_VehiculoFilter4' => '--PLEASE_SELECT--',
                'ctl00$PageContent$Precio_Venta_TotalFromFilter' => '',
                'ctl00$PageContent$Precio_Venta_TotalToFilter' => '',
                'ctl00$PageContent$Codigo_RadioFilter' => '',
                'ctl00$PageContent$Dms_ConsultaStockVehiculoViewPagination$_CurrentPage' => '2',
                'ctl00$PageContent$Dms_ConsultaStockVehiculoViewPagination$_PageSize' => '100',
                'ctl00$PageContent$ChangeWindowPopup' => '',
                'ctl00$_Include$_clientSideIsPostBack' => '',
                'ctl00$_Include$_ToolBar$_clientSideIsPostBack' => '',
                'ctl00$_Include$_ToolBar$_ChangeLanguage' => '',
                '__EVENTTARGET' => 'ctl00$PageContent$Dms_ConsultaStockVehiculoViewPagination$_PageSizeButton',
                '__EVENTARGUMENT' => '',
                '__LASTFOCUS' => 'ctl00_PageContent_Dms_ConsultaStockVehiculoViewPagination__PageSize',
                '__VIEWSTATE' => $viewstate["viewstate"],
                '__VIEWSTATEGENERATOR' => $viewstate["generator"],
                '__SCROLLPOSITIONX' => '0',
                '__SCROLLPOSITIONY' => '0',
                '__ASYNCPOST' => 'false'
//            ],
        ];

       /* $options = [
                'ctl00$scriptManager1=ctl00$PageContent$UpdatePanel1|ctl00$PageContent$Dms_ConsultaStockVehiculoViewPagination$_PageSizeButton&
                ctl00$pageLeftCoordinate=&
                ctl00$pageTopCoordinate=&
                ctl00$_mc_Header$_clientSideIsPostBack=N&
                ctl00$_mc_Header$_mcMenuSuperior$_clientSideIsPostBack=&
                ctl00$PageContent$_clientSideIsPostBack=&
                ctl00$PageContent$Dms_ConsultaStockVehiculoViewPanelExtender_ClientState=false&
                ctl00$PageContent$id_EmpresaFilter=205&
                ctl00$PageContent$id_SucursalFilter4=617&
                ctl00$PageContent$id_BodegaFilter4=--PLEASE_SELECT--&
                ctl00$PageContent$id_ProveedorFilter=&
                ctl00$PageContent$RBCompra=FechaCompraRB&
                ctl00$PageContent$Fecha_CompraFromFilter=&
                ctl00$PageContent$Fecha_CompraToFilter=&
                ctl00$PageContent$RBVenta=Fecha_VentaRb&
                ctl00$PageContent$Fecha_FacturacionFromFilter=&
                ctl00$PageContent$Fecha_FacturacionToFilter=&
                ctl00$PageContent$PagadoFilter=--PLEASE_SELECT--&
                ctl00$PageContent$id_Estado_UnidadFilter=--PLEASE_SELECT--&
                ctl00$PageContent$Placa_PatenteFilter=&
                ctl00$PageContent$Codigo_InternoFilter4=&
                ctl00$PageContent$Numero_ChasisFilter=&
                ctl00$PageContent$Numero_MotorFilter=&
                ctl00$PageContent$Numero_VINFilter=&
                ctl00$PageContent$id_MarcaFilter4=--PLEASE_SELECT--&
                ctl00$PageContent$id_Familia_ModeloFilter4=--PLEASE_SELECT--&
                ctl00$PageContent$id_ModeloFilter4=--PLEASE_SELECT--&
                ctl00$PageContent$Codigo_InternoFilter=&
                ctl00$PageContent$AñoFromFilter4=&
                ctl00$PageContent$AñoToFilter4=&
                ctl00$PageContent$id_Color_ExteriorFilter4=--PLEASE_SELECT--&
                ctl00$PageContent$id_Tipo_VehiculoFilter4=--PLEASE_SELECT--&
                ctl00$PageContent$Precio_Venta_TotalFromFilter=&
                ctl00$PageContent$Precio_Venta_TotalToFilter=&
                ctl00$PageContent$Codigo_RadioFilter=&
                ctl00$PageContent$Dms_ConsultaStockVehiculoViewPagination$_CurrentPage=2&
                ctl00$PageContent$Dms_ConsultaStockVehiculoViewPagination$_PageSize=100&
                ctl00$PageContent$ChangeWindowPopup=&
                ctl00$_Include$_clientSideIsPostBack=&
                ctl00$_Include$_ToolBar$_clientSideIsPostBack=&
                ctl00$_Include$_ToolBar$_ChangeLanguage=&
                __EVENTTARGET=ctl00$PageContent$Dms_ConsultaStockVehiculoViewPagination$_PageSizeButton&
                __EVENTARGUMENT=&
                __LASTFOCUS=ctl00_PageContent_Dms_ConsultaStockVehiculoViewPagination__PageSize&
                __VIEWSTATE='.$viewstate["viewstate"].'&
                __VIEWSTATEGENERATOR='.$viewstate["generator"].'&
                __SCROLLPOSITIONX=0&
                __SCROLLPOSITIONY=0&
                __ASYNCPOST=false'
        ];*/

        $options = http_build_query($options);

        $res = $this->get_site_html($url, $options, $headers, 'POST');

        dd($res);

    }

    public function login()
    {

        // login al sistema, genera las cookies con codigo de usuario
        $urlPrelogin = 'https://provider.autoprocloud.com/MC/home/mcHome.aspx/ValidaLogin';
        $headers = array(
            'Content-Type: application/json',
        );
        $body = '{
            "userName": "rodrigo.larrain@pompeyo.cl",
            "Password":"Xt!5LN"
        }';
        $respuesta = $this->get_site_html($urlPrelogin, $body, $headers);
        $respuesta = json_decode($respuesta);

        $userValidated = $respuesta->d->Message;

        // Segundo login, entrega pagina para viewstate
        $url = 'https://provider.autoprocloud.com/MC/home/mcHome.aspx/LogIn';
        $this->setCookie();

        $body = '{
          "businessID": "205",
          "BranchID": "672",
          "ModuleID": "2",
          "username": "rodrigo.larrain@pompeyo.cl"
        }';


//        $request = new Request('POST', $url, $headers, $body);
//        $res = $this->client->sendAsync($request)->wait();
        $respuesta = $this->get_site_html($url, $body, $headers);
//        $respuesta = $res->getBody();
        $respuesta = json_decode($respuesta);
        if($respuesta->d){
            $viewstate = $this->getViewstate($respuesta->d);
        }
        return $viewstate;
    }

    public function getViewstate($url){

        $respuesta = $this->get_site_html('https://provider.autoprocloud.com/MC/home/mcHome.aspx/LogIn', '', [], 'GET');

        $matches = [];

        $busqueda = '/id="__VIEWSTATE" value="([^"]+)"/';
        preg_match($busqueda, $respuesta, $viewstate);

        $busqueda = '/id="__VIEWSTATEGENERATOR" value="([^"]+)"/';
        preg_match($busqueda, $respuesta, $generator);

        return [
            "viewstate" => $viewstate[1] ?? null,
            "generator" => $generator[1] ?? null,
        ];

    }

}
