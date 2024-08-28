<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Api\ApiSolicitudController;
use App\Imports\ApcRepuestosImport;
use App\Imports\ApcSkuImport;
use App\Imports\ApcStockImport;
use App\Models\APC_Repuestos;
use App\Models\APC_Sku;
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
                if(!file_exists($this->cookie)) {
                    $fh = fopen($this->cookie, "w");
                    fwrite($fh, "");
                    fclose($fh);
                }
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
        define( 'WP_MEMORY_LIMIT', '300M' );
        define( 'WP_MAX_MEMORY_LIMIT', '300M' );

        echo "Inicio de proceso";
        Log::info('Inicio de proceso');

        $this->setCookie();

        // Login
        $viewstate = $this->login(4);
        if($viewstate) Log::info('Login OK');

        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        $filename = 'informeStock.xml';
        $filedata = Storage::get('public/viewstates/stockFull.json');

        $options['form_params'] = json_decode($filedata, true);
        $options['cookies'] = $this->cookieJar;
        $options['sink'] = storage_path('/app/public/' . $filename);

        print_r($options);

        if(file_exists(storage_path('/app/public/' . $filename))) {
            Log::info('Archivo existente');
            echo "Archivo existente".PHP_EOL;
            $res = true;
        }else{
            $request = new Request('POST', 'https://appspsa-cl.autoprocloud.com/vcl/Gestion/ShowDms_ConsultaStockTable.aspx', $headers);
//            $res = $this->client->sendAsync($request, $options)->wait();
            $res = $this->client->send($request, $options);
            Log::info('Archivo descargado');
            echo "Archivo descargado".PHP_EOL;
        }

//        if ($res) {
            echo "Informe descargado, procesando... ";
            Log::info('Procesando Informe');

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
                        $res = APC_Stock::create([
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
                        Log:info("Procesando " . $row['numero_vin']);
                    }

                    $numCell++;
                }
            }
            unlink(storage_path('/app/public/' . $filename));
            echo " Informe procesado";

//        }

//        Excel::import(new ApcStockImport(), $filename,  null, \Maatwebsite\Excel\Excel::XML);

    }

    public function traeSku()
    {

        set_time_limit(0);

        $this->setCookie();

        $url = 'https://appspsa-cl.autoprocloud.com/stk/dms_sku_kardex/ShowDms_SKU_Inventario_ValorizadoProcesosTable.aspx';

        // Login
        $viewstate = $this->login(4);

        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/png,image/svg+xml,*/*;q=0.8",
            'Accept-Encoding' => "gzip, deflate, br, zstd",
            'Connection' => "keep-alive",
            'Host' => "appspsa-cl.autoprocloud.com",
            'Origin' => "https://appspsa-cl.autoprocloud.com",
            'Referer' => $url,
            'Upgrade-Insecure-Requests' => "1",
            'User-Agent' => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36",
            'Sec-Fetch-Dest' => "document",
            'Sec-Fetch-Mode' => "navigate",
            'Sec-Fetch-Site' => "same-origin",
            'Sec-Fetch-User' => "?1",
            'cookie' => "_SelectedLanguage=es-cl; MC_SelectedLanguage=es-cl; ASP.NET_SessionId=5q0ommfu3xaebvijchhnoz1k; MCUserID=SqOjeXsr4Ds%3d; MCUsername=BrqOlO%2f7crG6MsfNalpelMdFBFY6cs9IwFGSLfmTmpM%3d; MCModuloID=%2b%2bUBtDC%2bg6U%3d; MCBusinessID=EEkNilVZQqQ%3d; MCBranchID=bPbWqSlsvZI%3d; BusinesCnn=x9ua6uagpNZM47bD5FZKci2IiJTRU5KAaHOqPg838vHVXK7%2bEACw3%2bjua7sfX5FNBwCIzpPDc8MdNwBflN42tyKjQxKo%2bzZ%2bV%2bElFyXXIwIXuyj6aXYTgAFA09RCXxXBUSo70zhJIWzudm3fmvD%2bNvlJDXyn7scl; MCLocalizacion=; APC-Nodo=02; ARRAffinity=2d343760a8ed36b0212d0c52481d1fee3a42070a07d1709749e873bd7238f130; ARRAffinitySameSite=2d343760a8ed36b0212d0c52481d1fee3a42070a07d1709749e873bd7238f130;",
        ];

        $filename = 'informeSku.xls';
        $filedata = Storage::get('public/viewstates/sku.json');

        $options['form_params'] = json_decode($filedata, true);
//        $options['cookies'] = $this->cookieJar;
        $options['sink'] = storage_path('/app/public/' . $filename);

        if (file_exists(storage_path('/app/public/' . $filename))) {
            $res = true;
        } else {
            $request = new Request('POST', $url, $headers);
            $res = $this->client->sendAsync($request, $options)->wait();
        }

        if($res) {
            APC_Sku::truncate();

            Excel::import(new ApcSkuImport(), storage_path('/app/public/' . $filename), null, \Maatwebsite\Excel\Excel::XLS);
        }

    }

    public function traeRepuestos()
    {

        set_time_limit(0);

        $this->setCookie();

        $url = 'https://appspsa-cl.autoprocloud.com/srv/dms_Calendario_Taller/ShowDms_SRV_InformeRepuestosEnProceso_TempTable.aspx';

        // Login
        $viewstate = $this->login(5);

        $sesion = $this->cookieJar->getCookieByName('ASP.NET_SessionId')->getValue();
        echo $sesion;

        $this->cookieJar->setCookie(new \GuzzleHttp\Cookie\SetCookie([
            'Domain' => '.autoprocloud.com',
            'Name' => 'ARRAffinity',
            'Value' => '2d343760a8ed36b0212d0c52481d1fee3a42070a07d1709749e873bd7238f130',
            'Path' => '/',
            'Expires' => null,
        ]));
        $this->cookieJar->setCookie(new \GuzzleHttp\Cookie\SetCookie([
            'Domain' => '.autoprocloud.com',
            'Name' => 'ARRAffinitySameSite',
            'Value' => '2d343760a8ed36b0212d0c52481d1fee3a42070a07d1709749e873bd7238f130',
            'Path' => '/',
            'Expires' => null,
        ]));
        $this->cookieJar->setCookie(new \GuzzleHttp\Cookie\SetCookie([
            'Domain' => '.autoprocloud.com',
            'Name' => '_apc_lastaction',
            'Value' => Carbon::now()->subHour()->format('D, d M Y H:i:s ').'GMT',
            'Path' => '/',
            'Expires' => null,
        ]));
        $this->cookieJar->setCookie(new \GuzzleHttp\Cookie\SetCookie([
            'Domain' => '.autoprocloud.com',
            'Name' => '_apc_endsession',
            'Value' => Carbon::now()->addDay()->format('D, d M Y H:i:s ').'GMT',
            'Path' => '/',
            'Expires' => null,
        ]));

        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/png,image/svg+xml,*/*;q=0.8",
            'Accept-Encoding' => "gzip, deflate, br, zstd",
            'Connection' => "keep-alive",
            'Host' => "appspsa-cl.autoprocloud.com",
            'Origin' => "https://appspsa-cl.autoprocloud.com",
            'Referer' => $url,
            'Upgrade-Insecure-Requests' => "1",
            'User-Agent' => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36",
            'Sec-Fetch-Dest' => "document",
            'Sec-Fetch-Mode' => "navigate",
            'Sec-Fetch-Site' => "same-origin",
            'cookie' => "MC_RememberName=True; MC_UserName=EBLWb+rNSN/HKBrDYHRxE+XD7kks2GSgeJuUVavNDNw=; MC_RememberPassword=False; MC_Password=; __utma=20487080.1977350873.1723474990.1724098633.1724184842.14; __utmz=20487080.1723474990.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); MC_SelectedLanguage=es-cl; MCUserID=SqOjeXsr4Ds%3d; MCModuloID=H51zpQpwDZM%3d; MCBusinessID=EEkNilVZQqQ%3d; MCBranchID=MpYSzVHxcro%3d; BusinesCnn=x9ua6uagpNZM47bD5FZKci2IiJTRU5KAaHOqPg838vHVXK7%2bEACw3%2bjua7sfX5FNBwCIzpPDc8MdNwBflN42tyKjQxKo%2bzZ%2bV%2bElFyXXIwIXuyj6aXYTgAFA09RCXxXBUSo70zhJIWzudm3fmvD%2bNvlJDXyn7scl; MCBusinessName=3ZhvsWjLTmzzD%2flzyrDlLi1CaosEOyax; MCBranchName=6wK%2fUIZPUTpMq7B0wpK0PQ%3d%3d; _ga=GA1.2.1977350873.1723474990; hblid=FQg4L7WjoPmcWFyw8H2zK0T1oKCSSBA2; _ga_C3HCJSVF27=GS1.2.1724184905.12.0.1724184905.0.0.0; olfsk=olfsk9432695658890976; _ga=GA1.3.1977350873.1723474990; __utmc=20487080; ARRAffinity=2d343760a8ed36b0212d0c52481d1fee3a42070a07d1709749e873bd7238f130; ARRAffinitySameSite=2d343760a8ed36b0212d0c52481d1fee3a42070a07d1709749e873bd7238f130; ASP.NET_SessionId=$sesion; MCUsername=BrqOlO%2f7crG6MsfNalpelMdFBFY6cs9IwFGSLfmTmpM%3d; APC-Nodo=02; MCLocalizacion=; wcsid=bUpzFFclkoMlDTj68H2zK0TBS12CKCA1; _oklv=1724185146771%2CbUpzFFclkoMlDTj68H2zK0TBS12CKCA1; _okdetect=%7B%22token%22%3A%2217241849068560%22%2C%22proto%22%3A%22about%3A%22%2C%22host%22%3A%22%22%7D; _ok=5154-826-10-2178; __utmb=20487080.1.10.1724184842; __utmt=1; _apc_lastaction=Tue, 20 Aug 2024 20:17:47 GMT; _apc_endsession=Tue, 20 Aug 2024 20:47:47 GMT; _gid=GA1.2.527458824.1724184905; _okbk=cd4%3Dtrue%2Cvi5%3D0%2Cvi4%3D1724184908246%2Cvi3%3Dactive%2Cvi2%3Dfalse%2Cvi1%3Dfalse%2Ccd8%3Dchat%2Ccd6%3D0%2Ccd5%3Daway%2Ccd3%3Dfalse%2Ccd2%3D0%2Ccd1%3D0%2C; _gid=GA1.3.527458824.1724184905",
        ];
//        print_r($headers);

        $request = new Request('GET', "https://provider.autoprocloud.com/mpi/mpi_empresa/showmpi_empresatable.aspx/SetSystemUse?id_Session=%22%22&Url=%22https://provider.autoprocloud.com/mc/Home/mcHome.aspx%22&SupportData=%22POMPEYO%20CARRASCO%20SPA|CITROEN%20QUILIN|RODRIGO%20LARRAIN%20ANDR%C3%89|0%200|rodrigo.larrain@pompeyo.cl|TallerPro|205|721|4967|5|$sesion|es-cl|02|AutoPro%22");
        $res = $this->client->sendAsync($request)->wait();
        echo $res->getBody();

        $filename = 'repuestosEnProceso.xls';
        $filedata = Storage::get('public/viewstates/repuestosEnProceso.json');

        $options['form_params'] = json_decode($filedata, true);
        $options['cookies'] = $this->cookieJar;
//        print_r($options['cookies']);
        $options['sink'] = storage_path('/app/public/' . $filename);

        if (file_exists(storage_path('/app/public/' . $filename ."__"))) {
            $res = true;
        } else {
            $request = new Request('POST', $url, $headers);
            $res = $this->client->sendAsync($request, $options)->wait();
        }

        if($res) {
//            echo $res->getBody();
            APC_Repuestos::truncate();

            Excel::import(new ApcRepuestosImport(), storage_path('/app/public/' . $filename), null, \Maatwebsite\Excel\Excel::XLS);
        }

    }

    public function traeMovimientosVentas()
    {

        set_time_limit(0);

        $this->setCookie();

        $url = 'https://appspsa-cl.autoprocloud.com/stk/Dms_SKU_ConsultaMovimientoVentas/ShowDms_SKU_ConsultaMovimientoVentasTable.aspx';

        // Login
        $viewstate = $this->login(4);
        $sesion = $this->cookieJar->getCookieByName('ASP.NET_SessionId');

        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/png,image/svg+xml,*/*;q=0.8",
            'Accept-Encoding' => "gzip, deflate, br, zstd",
            'Connection' => "keep-alive",
            'Host' => "appspsa-cl.autoprocloud.com",
            'Origin' => "https://appspsa-cl.autoprocloud.com",
            'Referer' => $url,
            'Upgrade-Insecure-Requests' => "1",
            'User-Agent' => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36",
            'Sec-Fetch-Dest' => "document",
            'Sec-Fetch-Mode' => "navigate",
            'Sec-Fetch-Site' => "same-origin",
            'cookie' => "_SelectedLanguage=es-cl; MC_SelectedLanguage=es-cl; MCUserID=SqOjeXsr4Ds%3d; MCUsername=BrqOlO%2f7crG6MsfNalpelMdFBFY6cs9IwFGSLfmTmpM%3d; MCModuloID=%2b%2bUBtDC%2bg6U%3d; MCBusinessID=EEkNilVZQqQ%3d; MCBranchID=9k326QgwWgg%3d; BusinesCnn=x9ua6uagpNZM47bD5FZKci2IiJTRU5KAaHOqPg838vHVXK7%2bEACw3%2bjua7sfX5FNBwCIzpPDc8MdNwBflN42tyKjQxKo%2bzZ%2bV%2bElFyXXIwIXuyj6aXYTgAFA09RCXxXBUSo70zhJIWzudm3fmvD%2bNvlJDXyn7scl;  ARRAffinity=2d343760a8ed36b0212d0c52481d1fee3a42070a07d1709749e873bd7238f130; ARRAffinitySameSite=2d343760a8ed36b0212d0c52481d1fee3a42070a07d1709749e873bd7238f130; ASP.NET_SessionId=mcbu1jf1wdxlkmgofr4yrmhm; MCLocalizacion=; APC-Nodo=02;",
        ];

        $filename = 'movimientoVentas.xls';
        $filedata = Storage::get('public/viewstates/movimientoVentas.json');

        $options['form_params'] = json_decode($filedata, true);
//        $options['cookies'] = $this->cookieJar;
        $options['sink'] = storage_path('/app/public/' . $filename);

        if (file_exists(storage_path('/app/public/' . $filename))) {
            $res = true;
        } else {
            $request = new Request('POST', $url, $headers);
            $res = $this->client->sendAsync($request, $options)->wait();
        }

        if($res) {
//            echo base64_decode($options['form_params']['__VIEWSTATE']);
//            APC_Repuestos::truncate();

//            Excel::import(new ApcRepuestosImport(), storage_path('/app/public/' . $filename), null, \Maatwebsite\Excel\Excel::XLS);
        }

    }

    public function login($modulo)
    {

        // login al sistema, genera las cookies con codigo de usuario
        $urlPrelogin = 'https://provider.autoprocloud.com/MC/home/mcHome.aspx/ValidaLogin';
        $headers = [
            'Content-Type' => 'application/json',
        ];

        $body = '{
            "userName": "rodrigo.larrain@pompeyo.cl",
            "Password":"Xt!5LN"
        }';
        $request = new Request('POST', $urlPrelogin, $headers, $body);
        $res = $this->client->sendAsync($request, ["cookies" => $this->cookieJar])->wait();
        $respuesta = $res->getBody();

        echo $respuesta;

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
          "ModuleID": "'.$modulo.'",
          "username": "rodrigo.larrain@pompeyo.cl"
        }';

        $request = new Request('POST', $url, $headers, $body);
        $res = $this->client->sendAsync($request, ["cookies" => $this->cookieJar])->wait();

        $respuesta = $res->getBody();
        $respuesta = json_decode($respuesta);
        if ($respuesta->d) {
            $viewstate = $this->getViewstate($respuesta->d);
        }


        // Ultima llamada para generar las cookies (HOME)
/*        $request = new Request('GET', "https://provider.autoprocloud.com/mc/default.aspx");
        $res = $this->client->send($request, ["cookies" => $this->cookieJar]);

        $request = new Request('GET', "https://provider.autoprocloud.com/mc/Home/mcHome.aspx");
        $res = $this->client->send($request, ["cookies" => $this->cookieJar]);*/

        return $viewstate;
    }

    public function getViewstate($url)
    {

        $request = new Request('GET', $url);
        $res = $this->client->sendAsync($request, ["cookies" => $this->cookieJar])->wait();
        $respuesta = $res->getBody();

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

    public function excel()
    {
        $client = new Client();


    }

}
