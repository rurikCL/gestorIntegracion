<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\ApiSolicitudController;
use App\Imports\ApcInformeOtImport;
use App\Imports\ApcMovimientoVentasImport;
use App\Imports\ApcRentabilidadVentasImport;
use App\Imports\ApcRepuestosImport;
use App\Imports\ApcSkuImport;
use App\Imports\ApcStockImport;
use App\Models\APC_MovimientoVentas;
use App\Models\APC_Repuestos;
use App\Models\APC_Sku;
use App\Models\APC_Stock;
use App\Models\MA\MA_Sucursales;
use App\Models\TDP_ApcStock;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Saloon\XmlWrangler\Exceptions\XmlReaderException;
use Saloon\XmlWrangler\XmlReader;
use SimpleXMLElement;


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
        Log::channel('robots')->info("definiendo cookie");
        $this->cookie = "cookiefileJar.txt";

        // si no existe el archivo, se crea
        if (!file_exists($this->cookie)) {
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
        ini_set('memory_limit', '1024M');

        echo "Inicio de proceso";
        Log::channel('robots')->info('Inicio de proceso stock APC');

        $this->setCookie();

        // Login
        $viewstate = $this->login(2);
        if ($viewstate) Log::channel('robots')->info('Login OK');

        $url = 'https://appspsa-cl.autoprocloud.com/vcl/Gestion/ShowDms_ConsultaStockTable.aspx';

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
        ];

        $filename = 'informeStock.xml';
        $filebase = Storage::get('public/viewstates/stockFullBase.json');
        $filedata = Storage::get('public/viewstates/stockFull.json');

        // Primer llamado
        $options['form_params'] = json_decode($filebase, true);
        $options['cookies'] = $this->cookieJar;
        $request = new Request('POST', $url, $headers);
        $res = $this->client->sendAsync($request, $options)->wait();

        Log::channel('robots')->info("Primer llamado OK. Iniciando descarga de informe");

        $options['form_params'] = json_decode($filedata, true);
        $options['cookies'] = $this->cookieJar;
        $options['sink'] = storage_path('/app/public/' . $filename);

//        print_r($options);

        if (file_exists(storage_path('/app/public/' . $filename))) {
            Log::channel('robots')->info('Archivo existente');
            echo "Archivo existente" . PHP_EOL;
            $res = true;
        } else {
            $request = new Request('POST', $url, $headers);
            $res = $this->client->sendAsync($request, $options)->wait();
//            $res = $this->client->send($request, $options);
            Log::channel('robots')->info('Archivo descargado');
            echo "Archivo descargado" . PHP_EOL;
        }

//        if ($res) {
        echo "Informe descargado, procesando... ";
        Log::channel('robots')->info('Procesando Informe');

        $filedata = Storage::read('/public/' . $filename);
        if ($filedata) {
            $xml = XmlReader::fromString(Storage::read('/public/' . $filename));
            $numCell = 0;
            $numCol = 0;

            APC_Stock::truncate();

            foreach ($xml->value('s:Row')->get() as $cell) {
                try {

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
                            'Kilometraje' => ($row['kilometraje']) ? $row['kilometraje'] : null,
                            'Codigo_Interno' => $row['codigo_interno'],
                            'Placa_Patente' => ($row['placa_patente']) ? $row['placa_patente'] : null,
                            'Condicion_Vehículo' => $row['condicion_vehiculo'],
                            'Color_Exterior' => $row['color_exterior'],
                            'Color_Interior' => $row['color_interior'],
                            'Precio_Venta_Total' => ($row['precio_venta_total'] != '') ? $row['precio_venta_total'] : null,
                            'Estado_AutoPro' => ($row['estado_autopro']) ? $row['estado_autopro'] : null,
                            'Dias_Stock' => ($row['dias_stock'] != '') ? $row['dias_stock'] : null,
                            'Estado_Dealer' => ($row['estado_dealer']) ? $row['estado_dealer'] : null,
                            'Bodega' => $row['bodega'],
                            'Equipamiento' => $row['equipamiento'],
                            'Numero_Motor' => $row['numero_motor'],
                            'Numero_Chasis' => $row['numero_chasis'],
                            'Proveedor' => $row['proveedor'],
                            'Fecha_Disponibilidad' => ($row['fecha_disponibilidad'] != '') ? Carbon::createFromFormat("d-m-Y H:i:s", $row['fecha_disponibilidad'])->format('Y-m-d H:i:s') : null,
                            'Factura_Compra' => ($row['factura_compra'] != '') ? ($row['factura_compra'] ?? 0) : null,
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
//                            Log::channel('robots')->info("Procesando " . $row['numero_vin']);
                    }

                    $numCell++;
                } catch (\Exception $e) {
                    Log::channel('robots')->error("Error con registro " . $row['numero_vin'] . " : " . $e->getMessage());

                }
            }

        }
        unlink(storage_path('/app/public/' . $filename));
        echo " Informe procesado";

    }

    public function traeStockAnual()
    {
        set_time_limit(0);
        ini_set('memory_limit', '2048M');
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        echo "Inicio de proceso";
        Log::channel('robots')->info('Inicio de proceso stock APC');

        $this->setCookie();

        // Login
        $viewstate = $this->login(2);
        if ($viewstate) Log::channel('robots')->info('Login OK');

        $url = 'https://appspsa-cl.autoprocloud.com/vcl/Gestion/ShowDms_ConsultaStockTable.aspx';

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
        ];

        $periodos = [
//            2020,
            2021,
            2022,
            2023,
            2024,
            2025
        ];

//        TDP_ApcStock::truncate();

        foreach ($periodos as $periodo) {
            Log::channel('robots')->info("Procesando periodo $periodo");

            // nombre de informe descargado
            $archivo = "informeStock" . $periodo . ".xml";

            // Primer llamado
            $options = [];
            $filebase = Storage::get('public/viewstates/stock' . $periodo . 'base.json');
            $options['form_params'] = json_decode($filebase, true);
            $options['cookies'] = $this->cookieJar;
            $request = new Request('POST', $url, $headers);
            $res = $this->client->sendAsync($request, $options)->wait();

            // Llamado descarga excel
            $options = [];
            $filedata = Storage::get('public/viewstates/stock' . $periodo . '.json');
            $options['form_params'] = json_decode($filedata, true);
            $options['cookies'] = $this->cookieJar;
            $options['sink'] = storage_path('/app/public/' . $archivo);

            $request = new Request('POST', $url, $headers);
            $res = $this->client->sendAsync($request, $options)->wait();

            Log::channel('robots')->info('Archivo descargado');
            echo "Archivo descargado, procesando $periodo..." . PHP_EOL;


            Log::channel('robots')->info("Procesando /public/$archivo ");
            $filedata = Storage::read('/public/' . $archivo);

            if ($filedata) {
//                $xml = XmlReader::fromString(Storage::read('/public/' . $archivo));
                /*$xml = simplexml_load_string(Storage::read('/public/' . $archivo));

                foreach($xml->children() as $child) {
                    echo $child;
                }*/

                try {
                    $xml = XmlReader::fromFile(storage_path('/app/public/' . $archivo));
                } catch (XmlReaderException $e) {
                    Log::channel('robots')->info($e->getMessage());
                    Log::channel('robots')->error("No se pudo cargar el archivo XML");
                    $xml = null;
                }
                $numCell = 0;
                $numCol = 0;

                if ($xml) {
                    foreach ($xml->value('s:Row')->get() as $cell) {

                        try {

                            $numCol = 0;
                            foreach ($cell['s:Cell'] as $data) {

                                if ($numCell > 0) {
                                    $dataArray[$numCell][$headers[$numCol]] = $data['s:Data'];

                                } else {
                                    $headers[$numCol] = Str::slug($data['s:Data'], '_');
                                }
                                $numCol++;
                            }

                            if ($numCell > 0 and $dataArray[$numCell]['codigo_interno'] != '') {
                                $row = $dataArray[$numCell];

                                $res = APC_Stock::updateOrCreate([
                                    'Codigo_Interno' => $row['codigo_interno'],
                                    'Empresa' => $row['empresa'],
                                ], [
                                    'Empresa' => $row['empresa'],
                                    'Sucursal' => $row['sucursal'],
                                    'Folio_Venta' => ($row['folio_venta'] != '') ? $row['folio_venta'] : null,
                                    'Venta' => ($row['venta'] != '') ? $row['venta'] : null,
                                    'Estado_Venta' => $row['estado_venta'] ?? null,
                                    'Fecha_Venta' => ($row['fecha_venta'] != '') ? Carbon::createFromFormat("d-m-Y H:i:s", $row['fecha_venta'])->format('Y-m-d H:i:s') : null,
                                    'Tipo_Documento' => $row['tipo_documento_folio'] ?? null,
                                    'Vendedor' => $row['vendedor'] ?? null,
                                    'Fecha_Ingreso' => ($row['fecha_ingreso'] != '') ? Carbon::createFromFormat("d-m-Y H:i:s", $row['fecha_ingreso'])->format('Y-m-d H:i:s') : null,
                                    'Fecha_Facturacion' => ($row['fecha_facturacion'] != '') ? Carbon::createFromFormat("d-m-Y H:i:s", $row['fecha_facturacion'])->format('Y-m-d H:i:s') : null,
                                    'VIN' => $row['numero_vin'],
                                    'Marca' => $row['marca'],
                                    'Modelo' => $row['modelo'],
                                    'Version' => $row['version'],
                                    'Codigo_Version' => $row['codigo_version'],
                                    'Anio' => ($row['ano'] != '') ? $row['ano'] : null,
                                    'Kilometraje' => $row['kilometraje'] ?? null,
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
                                    'Factura_Compra' => ($row['factura_compra'] != '') ? ($row['factura_compra'] ?? 0) : null,
                                    'Vencimiento_Documento' => ($row['vencimiento_documento'] != '') ? Carbon::createFromFormat("d-m-Y H:i:s", $row['vencimiento_documento'])->format('Y-m-d H:i:s') : null,
                                    'Fecha_Compra' => ($row['fecha_compra'] != '') ? Carbon::createFromFormat("d-m-Y H:i:s", $row['fecha_compra'])->format('Y-m-d H:i:s') : null,
                                    'Fecha_Vencto_Rev_tec' => ($row['fecha_vencto_revision_tecnica'] != '') ? Carbon::createFromFormat("d-m-Y H:i:s", $row['fecha_vencto_revision_tecnica'])->format('Y-m-d H:i:s') : null,
                                    'N_Propietarios' => $row['n_propietarios'] ?? null,
                                    'Folio_Retoma' => ($row['folio_retoma'] != '') ? $row['folio_retoma'] : null,
                                    'Fecha_Retoma' => ($row['fecha_retoma'] != '') ? Carbon::createFromFormat("d-m-Y H:i:s", $row['fecha_retoma'])->format('Y-m-d H:i:s') : null,
                                    'Dias_Reservado' => $row['dias_reservado'] ?? null,
                                    'Precio_Compra_Neto' => ($row['precio_compra_neto'] != '') ? $row['precio_compra_neto'] : null,
                                    'Gasto' => $row['gasto'],
                                    'Accesorios' => $row['accesorios'],
                                    'Total_Costo' => ($row['total_costo'] != '') ? $row['total_costo'] : null,
                                    'Precio_Lista' => ($row['precio_lista'] != '') ? $row['precio_lista'] : null,
                                    'Margen' => null,
//                                'Margen' => ($row['margen'] != '') ? intval($row['margen']) : null,
//            'Margen_porcentaje' => ($row['margen'] != '') ? $row['margen'] : null,
                                ]);
                            } else {
                                if ($numCell == 0)
                                    Log::channel('robots')->info("Xml valido - cabeceras procesadas");
                            }

                            $numCell++;
                        } catch (\Exception $e) {
                            Log::channel('robots')->error("Error con registro " . $row['numero_vin'] . " : " . $e->getMessage());

                        }
                    }


                }

            }
            unlink(storage_path('/app/public/' . $archivo));
            Log::channel('robots')->info("Informe procesado");
            echo " Informe procesado";

            // Ejecucion de callback after
            $solicitudObj = new ApiSolicitudController();
            $res = $solicitudObj->urlCallParam("https://apps2.pompeyo.cl/api/cpd/actualizarubicacion", "POST");

        }

        Log::channel('robots')->info("Fin de proceso Stock");

    }


    public function traeStockAll()
    {
        set_time_limit(0);
        ini_set('memory_limit', '2048M');
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        echo "Inicio de proceso";
        Log::channel('robots')->info('Inicio de proceso stock APC');

        $this->setCookie();

        // Login
        $viewstate = $this->login(2);
        if ($viewstate) Log::channel('robots')->info('Login OK');

        $url = 'https://appspsa-cl.autoprocloud.com/vcl/Gestion/ShowDms_ConsultaStockTable.aspx';

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
        ];

        $periodos = [
            2024, 2025
        ];

//        TDP_ApcStock::truncate();

        foreach ($periodos as $periodo) {
            Log::channel('robots')->info("Procesando periodo $periodo");

            // nombre de informe descargado
            $archivo = "informeStockAll" . $periodo . ".xml";

            // Primer llamado
            $options = [];
            $filebase = Storage::get('public/viewstates/stockAllBase.json');
            $options['form_params'] = json_decode($filebase, true);
            $options['cookies'] = $this->cookieJar;
            $options['form_params']['ctl00$PageContent$Fecha_CompraFromFilter'] = Carbon::createFromDate($periodo)->firstOfYear()->format('d-m-Y');
            $options['form_params']['ctl00$PageContent$Fecha_CompraToFilter'] = Carbon::createFromDate($periodo)->lastOfYear()->format('d-m-Y');
            $request = new Request('POST', $url, $headers);
            $res = $this->client->sendAsync($request, $options)->wait();

            // Llamado descarga excel
            $options = [];
            $filedata = Storage::get('public/viewstates/stockAll.json');
            $options['form_params'] = json_decode($filedata, true);
            $options['cookies'] = $this->cookieJar;
            $options['sink'] = storage_path('/app/public/' . $archivo);

            $request = new Request('POST', $url, $headers);
            $res = $this->client->sendAsync($request, $options)->wait();

            Log::channel('robots')->info('Archivo descargado');
            echo "Archivo descargado, procesando $periodo..." . PHP_EOL;


            Log::channel('robots')->info("Procesando /public/$archivo ");
            $filedata = Storage::read('/public/' . $archivo);

            if ($filedata) {
//                $xml = XmlReader::fromString(Storage::read('/public/' . $archivo));
                /*$xml = simplexml_load_string(Storage::read('/public/' . $archivo));

                foreach($xml->children() as $child) {
                    echo $child;
                }*/

                try {
                    $xml = XmlReader::fromFile(storage_path('/app/public/' . $archivo));
                } catch (XmlReaderException $e) {
                    Log::channel('robots')->info($e->getMessage());
                    Log::channel('robots')->error("No se pudo cargar el archivo XML");
                    $xml = null;
                }
                $numCell = 0;
                $numCol = 0;

                if ($xml) {
                    foreach ($xml->value('s:Row')->get() as $cell) {

                        try {

                            $numCol = 0;
                            foreach ($cell['s:Cell'] as $data) {

                                if ($numCell > 0) {
                                    $dataArray[$numCell][$headers[$numCol]] = $data['s:Data'];

                                } else {
                                    $headers[$numCol] = Str::slug($data['s:Data'], '_');
                                }
                                $numCol++;
                            }

                            if ($numCell > 0 and $dataArray[$numCell]['codigo_interno'] != '') {
                                $row = $dataArray[$numCell];

                                $res = APC_Stock::updateOrCreate([
                                    'Codigo_Interno' => $row['codigo_interno'],
                                    'Empresa' => $row['empresa'],
                                ], [
                                    'Empresa' => $row['empresa'],
                                    'Sucursal' => $row['sucursal'],
                                    'Folio_Venta' => ($row['folio_venta'] != '') ? $row['folio_venta'] : null,
                                    'Venta' => ($row['venta'] != '') ? $row['venta'] : null,
                                    'Estado_Venta' => $row['estado_venta'] ?? null,
                                    'Fecha_Venta' => ($row['fecha_venta'] != '') ? Carbon::createFromFormat("d-m-Y H:i:s", $row['fecha_venta'])->format('Y-m-d H:i:s') : null,
                                    'Tipo_Documento' => $row['tipo_documento_folio'] ?? null,
                                    'Vendedor' => $row['vendedor'] ?? null,
                                    'Fecha_Ingreso' => ($row['fecha_ingreso'] != '') ? Carbon::createFromFormat("d-m-Y H:i:s", $row['fecha_ingreso'])->format('Y-m-d H:i:s') : null,
                                    'Fecha_Facturacion' => ($row['fecha_facturacion'] != '') ? Carbon::createFromFormat("d-m-Y H:i:s", $row['fecha_facturacion'])->format('Y-m-d H:i:s') : null,
                                    'VIN' => $row['numero_vin'],
                                    'Marca' => $row['marca'],
                                    'Modelo' => $row['modelo'],
                                    'Version' => $row['version'],
                                    'Codigo_Version' => $row['codigo_version'],
                                    'Anio' => ($row['ano'] != '') ? $row['ano'] : null,
                                    'Kilometraje' => $row['kilometraje'] ?? null,
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
                                    'Factura_Compra' => ($row['factura_compra'] != '') ? ($row['factura_compra'] ?? 0) : null,
                                    'Vencimiento_Documento' => ($row['vencimiento_documento'] != '') ? Carbon::createFromFormat("d-m-Y H:i:s", $row['vencimiento_documento'])->format('Y-m-d H:i:s') : null,
                                    'Fecha_Compra' => ($row['fecha_compra'] != '') ? Carbon::createFromFormat("d-m-Y H:i:s", $row['fecha_compra'])->format('Y-m-d H:i:s') : null,
                                    'Fecha_Vencto_Rev_tec' => ($row['fecha_vencto_revision_tecnica'] != '') ? Carbon::createFromFormat("d-m-Y H:i:s", $row['fecha_vencto_revision_tecnica'])->format('Y-m-d H:i:s') : null,
                                    'N_Propietarios' => $row['n_propietarios'] ?? null,
                                    'Folio_Retoma' => ($row['folio_retoma'] != '') ? $row['folio_retoma'] : null,
                                    'Fecha_Retoma' => ($row['fecha_retoma'] != '') ? Carbon::createFromFormat("d-m-Y H:i:s", $row['fecha_retoma'])->format('Y-m-d H:i:s') : null,
                                    'Dias_Reservado' => $row['dias_reservado'] ?? null,
                                    'Precio_Compra_Neto' => ($row['precio_compra_neto'] != '') ? $row['precio_compra_neto'] : null,
                                    'Gasto' => $row['gasto'],
                                    'Accesorios' => $row['accesorios'],
                                    'Total_Costo' => ($row['total_costo'] != '') ? $row['total_costo'] : null,
                                    'Precio_Lista' => ($row['precio_lista'] != '') ? $row['precio_lista'] : null,
                                    'Margen' => null,
//                                'Margen' => ($row['margen'] != '') ? intval($row['margen']) : null,
//            'Margen_porcentaje' => ($row['margen'] != '') ? $row['margen'] : null,
                                ]);
                            } else {
                                if ($numCell == 0)
                                    Log::channel('robots')->info("Xml valido - cabeceras procesadas");
                            }

                            $numCell++;
                        } catch (\Exception $e) {
                            Log::channel('robots')->error("Error con registro " . $row['numero_vin'] . " : " . $e->getMessage());

                        }
                    }


                }

            }
            unlink(storage_path('/app/public/' . $archivo));
            Log::channel('robots')->info("Informe procesado");
            echo " Informe procesado";

            // Ejecucion de callback after
            $solicitudObj = new ApiSolicitudController();
            $res = $solicitudObj->urlCallParam("https://apps2.pompeyo.cl/api/cpd/actualizarubicacion", "POST");

        }

        Log::channel('robots')->info("Fin de proceso Stock");

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
        ];

        $filename = 'informeSku.xls';
        $filebase = Storage::get('public/viewstates/skuBase.json');
        $filedata = Storage::get('public/viewstates/sku.json');

        // Primer llamado
        $options['form_params'] = json_decode($filebase, true);
        $options['cookies'] = $this->cookieJar;
        $options['form_params']['ctl00$PageContent$FechaFilter'] = Carbon::yesterday()->format('d-m-Y');
        $request = new Request('POST', $url, $headers);
        $res = $this->client->sendAsync($request, $options)->wait();

        //Trae Excel
        $options['form_params'] = json_decode($filedata, true);
        $options['cookies'] = $this->cookieJar;
        $options['form_params']['ctl00$PageContent$FechaFilter'] = Carbon::yesterday()->format('d-m-Y');
        $options['sink'] = storage_path('/app/public/' . $filename);

        if (file_exists(storage_path('/app/public/' . $filename))) {
            $res = true;
        } else {
            $request = new Request('POST', $url, $headers);
            $res = $this->client->sendAsync($request, $options)->wait();
        }

        if ($res) {
            APC_Sku::truncate();
            Excel::import(new ApcSkuImport(), storage_path('/app/public/' . $filename), null, \Maatwebsite\Excel\Excel::XLS);
            unlink(storage_path('/app/public/' . $filename));
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
        ];

        $filename = 'repuestosEnProceso.xls';
        $filebase = Storage::get('public/viewstates/repuestosBase.json');
        $filedata = Storage::get('public/viewstates/repuestosEnProceso.json');

        // Primer llamado  -----------------------------------------------
        $options['form_params'] = json_decode($filebase, true);
        $options['cookies'] = $this->cookieJar;
        $request = new Request('POST', $url, $headers);
        $res = $this->client->sendAsync($request, $options)->wait();

        // Llamada Excel -----------------------------------------------

        $options['form_params'] = json_decode($filedata, true);
        $options['cookies'] = $this->cookieJar;
        $options['sink'] = storage_path('/app/public/' . $filename);

        if (file_exists(storage_path('/app/public/' . $filename))) {
            $res = true;
        } else {
            echo "preparando descarga de informe ";
            $request = new Request('POST', $url, $headers);
            $res = $this->client->sendAsync($request, $options)->wait();
        }

        if ($res) {
            echo "informe desccargado, procesando... ";
            APC_Repuestos::truncate();
            Excel::import(new ApcRepuestosImport(), storage_path('/app/public/' . $filename), null, \Maatwebsite\Excel\Excel::XLS);
            unlink(storage_path('/app/public/' . $filename));


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
        ];

        $filename = 'movimientoVentas.xls';
        $filebase = Storage::get('public/viewstates/movimientoVentasBase.json');
        $filedata = Storage::get('public/viewstates/movimientoVentas.json');


        // Primer llamado
        $options['form_params'] = json_decode($filebase, true);
        $options['cookies'] = $this->cookieJar;
        $options['form_params']['ctl00$PageContent$Fecha_DocumentoFromFilter'] = Carbon::now()->firstOfMonth()->format('d-m-Y');
        $options['form_params']['ctl00$PageContent$Fecha_DocumentoToFilter'] = Carbon::now()->lastOfMonth()->format('d-m-Y');
        $request = new Request('POST', $url, $headers);
        $res = $this->client->sendAsync($request, $options)->wait();


        // Excel
        $options['form_params'] = json_decode($filedata, true);
        $options['form_params']['ctl00$PageContent$Fecha_DocumentoFromFilter'] = Carbon::now()->firstOfMonth()->format('d-m-Y');
        $options['form_params']['ctl00$PageContent$Fecha_DocumentoToFilter'] = Carbon::now()->lastOfMonth()->format('d-m-Y');
        $options['cookies'] = $this->cookieJar;
        $options['sink'] = storage_path('/app/public/' . $filename);

        if (file_exists(storage_path('/app/public/' . $filename))) {
            $res = true;
        } else {
            $request = new Request('POST', $url, $headers);
            $res = $this->client->sendAsync($request, $options)->wait();
        }

        if ($res) {
            Excel::import(new ApcMovimientoVentasImport(), storage_path('/app/public/' . $filename), null, \Maatwebsite\Excel\Excel::XLS);
            unlink(storage_path('/app/public/' . $filename));

        }

    }

    public function traeRentabilidadOt()
    {
        set_time_limit(0);
        $this->setCookie();

        $url = 'https://appspsa-cl.autoprocloud.com/srv/Gestion/ShowDms_OT_RentabilidadFacturadasTable.aspx';

        // Login
        $viewstate = $this->login(5);
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
            'Sec-Fetch-User' => "?1",
        ];

        $filename = 'rentabilidadOt.xls';
        $filebase = Storage::get('public/viewstates/rentabilidadOtBase.json');
        $filedata = Storage::get('public/viewstates/rentabilidadOT.json');


        // Primer llamado
        $options['form_params'] = json_decode($filebase, true);
        $options['cookies'] = $this->cookieJar;
//        $options['form_params']['ctl00$PageContent$Fecha_FacturacionFromFilter'] = Carbon::now()->firstOfMonth()->format('d-m-Y');
//        $options['form_params']['ctl00$PageContent$Fecha_FacturacionToFilter'] = Carbon::now()->lastOfMonth()->format('d-m-Y');
        $request = new Request('POST', $url, $headers);
        $res = $this->client->sendAsync($request, $options)->wait();


        // Excel
        $options['form_params'] = json_decode($filedata, true);
//        $options['form_params']['ctl00$PageContent$Fecha_FacturacionFromFilter'] = Carbon::now()->firstOfMonth()->format('d-m-Y');
//        $options['form_params']['ctl00$PageContent$Fecha_FacturacionToFilter'] = Carbon::now()->lastOfMonth()->format('d-m-Y');
        $options['cookies'] = $this->cookieJar;
        $options['sink'] = storage_path('/app/public/' . $filename);

        if (file_exists(storage_path('/app/public/' . $filename . "__"))) {
            $res = true;
        } else {
            $request = new Request('POST', $url, $headers);
            $res = $this->client->sendAsync($request, $options)->wait();
        }

        if ($res) {
//            APC_MovimientoVentas::truncate();
//            Excel::import(new ApcMovimientoVentasImport(), storage_path('/app/public/' . $filename), null, \Maatwebsite\Excel\Excel::XLS);
//            unlink(storage_path('/app/public/' . $filename));

        }

    }

    public function traeRentabilidadSku()
    {
        set_time_limit(0);
        $this->setCookie();

        $url = 'https://appspsa-cl.autoprocloud.com/srv/dms_ot_rentabilidad/showdms_ot_rentabilidad_skutable.aspx';

        // Login
        $viewstate = $this->login(5);
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

        ];

        $filename = 'rentabilidadSku.xls';
        $filebase = Storage::get('public/viewstates/rentabilidadSkuBase.json');
        $filedata = Storage::get('public/viewstates/rentabilidadSku.json');


        // Primer llamado
        $options['form_params'] = json_decode($filebase, true);
        $options['cookies'] = $this->cookieJar;
        $options['form_params']['ctl00$PageContent$Fecha_FacturacionFromFilter'] = Carbon::now()->firstOfMonth()->format('d-m-Y');
        $options['form_params']['ctl00$PageContent$Fecha_FacturacionToFilter'] = Carbon::now()->lastOfMonth()->format('d-m-Y');
        $request = new Request('POST', $url, $headers);
        $res = $this->client->sendAsync($request, $options)->wait();


        // Excel
        $options['form_params'] = json_decode($filedata, true);
        $options['form_params']['ctl00$PageContent$Fecha_FacturacionFromFilter'] = Carbon::now()->firstOfMonth()->format('d-m-Y');
        $options['form_params']['ctl00$PageContent$Fecha_FacturacionToFilter'] = Carbon::now()->lastOfMonth()->format('d-m-Y');
        $options['cookies'] = $this->cookieJar;
        $options['sink'] = storage_path('/app/public/' . $filename);

        if (file_exists(storage_path('/app/public/' . $filename . "__"))) {
            $res = true;
        } else {
            $request = new Request('POST', $url, $headers);
            $res = $this->client->sendAsync($request, $options)->wait();
        }

        if ($res) {
            Excel::import(new ApcMovimientoVentasImport(), storage_path('/app/public/' . $filename), null, \Maatwebsite\Excel\Excel::XLS);
            unlink(storage_path('/app/public/' . $filename));

        }

    }

    public function traeInformeOt()
    {
        set_time_limit(0);
        $this->setCookie();

        $url = 'https://appspsa-cl.autoprocloud.com/srv/Gestion/ShowDms_OT_InformeOTTable.aspx';

        // Login
        $viewstate = $this->login(5);
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

        ];

        $filename = 'informeOT.xls';
        $filebase = Storage::get('public/viewstates/informeOtSemanalBase.json');
        $filedata = Storage::get('public/viewstates/informeOtSemanal.json');
//        $filebase = Storage::get('public/viewstates/informeOtBase.json');
//        $filedata = Storage::get('public/viewstates/informeOt.json');


        // Primer llamado
        $options['form_params'] = json_decode($filebase, true);
        $options['cookies'] = $this->cookieJar;
        $options['form_params']['ctl00$PageContent$FechaFromFilter'] = Carbon::now()->firstOfYear()->format('d-m-Y');
        $options['form_params']['ctl00$PageContent$FechaToFilter'] = Carbon::now()->lastOfMonth()->format('d-m-Y');
//        $options['form_params']['ctl00$PageContent$FechaFromFilter'] = Carbon::now()->subDays(1)->format('d-m-Y');
//        $options['form_params']['ctl00$PageContent$FechaToFilter'] = Carbon::now()->format('d-m-Y');
        $request = new Request('POST', $url, $headers);
        $res = $this->client->sendAsync($request, $options)->wait();


        // Excel
        $options['form_params'] = json_decode($filedata, true);
        $options['form_params']['ctl00$PageContent$FechaFromFilter'] = Carbon::now()->firstOfYear()->format('d-m-Y');
        $options['form_params']['ctl00$PageContent$FechaToFilter'] = Carbon::now()->lastOfMonth()->format('d-m-Y');
//        $options['form_params']['ctl00$PageContent$FechaFromFilter'] = Carbon::now()->subDays(1)->format('d-m-Y');
//        $options['form_params']['ctl00$PageContent$FechaToFilter'] = Carbon::now()->format('d-m-Y');

        $options['cookies'] = $this->cookieJar;
        $options['sink'] = storage_path('/app/public/' . $filename);

        if (file_exists(storage_path('/app/public/' . $filename . "__"))) {
            $res = true;
        } else {
            $request = new Request('POST', $url, $headers);
            $res = $this->client->sendAsync($request, $options)->wait();
        }

        if ($res) {
            Excel::import(new ApcInformeOtImport(), storage_path('/app/public/' . $filename), null, \Maatwebsite\Excel\Excel::XLS);
            unlink(storage_path('/app/public/' . $filename));

        }

    }

    public function traeInformeOtAcotado()
    {
        set_time_limit(0);
        $this->setCookie();

        $url = 'https://appspsa-cl.autoprocloud.com/srv/Gestion/ShowDms_OT_InformeOTTable.aspx';

        // Login
        $viewstate = $this->login(5);
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

        ];

        $filename = 'informeOTAcotado.xls';
//        $filebase = Storage::get('public/viewstates/informeOtSemanalBase.json');
//        $filedata = Storage::get('public/viewstates/informeOtSemanal.json');
        $filebase = Storage::get('public/viewstates/informeOTAllBase.json');
        $filedata = Storage::get('public/viewstates/informeOTAll.json');


        // Primer llamado
        $options['form_params'] = json_decode($filebase, true);
        $options['cookies'] = $this->cookieJar;
//        $options['form_params']['ctl00$PageContent$FechaFromFilter'] = Carbon::now()->firstOfMonth()->format('d-m-Y');
//        $options['form_params']['ctl00$PageContent$FechaToFilter'] = Carbon::now()->lastOfMonth()->format('d-m-Y');
        $options['form_params']['ctl00$PageContent$FechaFromFilter'] = Carbon::now()->subDays(1)->format('d-m-Y');
        $options['form_params']['ctl00$PageContent$FechaToFilter'] = Carbon::now()->addDay()->format('d-m-Y');
        $request = new Request('POST', $url, $headers);
        $res = $this->client->sendAsync($request, $options)->wait();


        // Excel
        $options['form_params'] = json_decode($filedata, true);
//        $options['form_params']['ctl00$PageContent$FechaFromFilter'] = Carbon::now()->firstOfMonth()->format('d-m-Y');
//        $options['form_params']['ctl00$PageContent$FechaToFilter'] = Carbon::now()->lastOfMonth()->format('d-m-Y');
        $options['form_params']['ctl00$PageContent$FechaFromFilter'] = Carbon::now()->subDays(1)->format('d-m-Y');
        $options['form_params']['ctl00$PageContent$FechaToFilter'] = Carbon::now()->format('d-m-Y');

        $options['cookies'] = $this->cookieJar;
        $options['sink'] = storage_path('/app/public/' . $filename);

        if (file_exists(storage_path('/app/public/' . $filename . "__"))) {
            $res = true;
        } else {
            $request = new Request('POST', $url, $headers);
            $res = $this->client->sendAsync($request, $options)->wait();
        }

        if ($res) {
            Excel::import(new ApcInformeOtImport(), storage_path('/app/public/' . $filename), null, \Maatwebsite\Excel\Excel::XLS);
            unlink(storage_path('/app/public/' . $filename));

        }

    }

    public function traeRentabilidadVenta()
    {
        set_time_limit(0);
        $this->setCookie();

        $url = 'https://appspsa-cl.autoprocloud.com/ftc/dms_gestion/rentabilidad_repuestos_detalle.aspx';

        // Login
        $viewstate = $this->login(5);
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

        ];

        $filename = 'rentabilidadVenta.xls';
        $filebase = Storage::get('public/viewstates/rentabilidadVentaBase.json');
        $filedata = Storage::get('public/viewstates/rentabilidadVenta.json');


        // Primer llamado
        $options['form_params'] = json_decode($filebase, true);
        $options['cookies'] = $this->cookieJar;
        $options['form_params']['ctl00$PageContent$Fecha_FacturacionFromFilter1'] = Carbon::now()->firstOfMonth()->format('d-m-Y');
        $options['form_params']['ctl00$PageContent$Fecha_FacturacionToFilter1'] = Carbon::now()->lastOfMonth()->format('d-m-Y');
        $request = new Request('POST', $url, $headers);
        $res = $this->client->sendAsync($request, $options)->wait();


        // Excel
        $options['form_params'] = json_decode($filedata, true);
        $options['form_params']['ctl00$PageContent$Fecha_FacturacionFromFilter1'] = Carbon::now()->firstOfMonth()->format('d-m-Y');
        $options['form_params']['ctl00$PageContent$Fecha_FacturacionToFilter1'] = Carbon::now()->lastOfMonth()->format('d-m-Y');
        $options['cookies'] = $this->cookieJar;
        $options['sink'] = storage_path('/app/public/' . $filename);

        if (file_exists(storage_path('/app/public/' . $filename . "__"))) {
            $res = true;
        } else {
            $request = new Request('POST', $url, $headers);
            $res = $this->client->sendAsync($request, $options)->wait();
        }

        if ($res) {
            Excel::import(new ApcRentabilidadVentasImport(), storage_path('/app/public/' . $filename), null, \Maatwebsite\Excel\Excel::XLS);
            unlink(storage_path('/app/public/' . $filename));

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

        $body = '{
          "businessID": "205",
          "BranchID": "672",
          "ModuleID": "' . $modulo . '",
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

    public function compare()
    {

        $reasons = array();
        $xml1 = new SimpleXMLElement(Storage::read('public/informeStock2024.xml'));
        $xml2 = new SimpleXMLElement(Storage::read('public/informeStock2024_old.xml'));
        $result = $this->xml_is_equal($xml1, $xml2);
        if ($result === true) {
            // the XML documents are the same
            print ('iguales');
        } else {
            // they are different: print the reason why
            printf(STDERR, "XML documents are different: $result");
        }

    }




    function xml_is_equal(SimpleXMLElement $xml1, SimpleXMLElement $xml2, $text_strict = false) {
        // compare text content
        if ($text_strict) {
            if ("$xml1" != "$xml2") return "mismatched text content (strict)";
        } else {
            if (trim("$xml1") != trim("$xml2")) return "mismatched text content";
        }

        // check all attributes
        $search1 = array();
        $search2 = array();
        foreach ($xml1->attributes() as $a => $b) {
            $search1[$a] = "$b";		// force string conversion
        }
        foreach ($xml2->attributes() as $a => $b) {
            $search2[$a] = "$b";
        }
        if ($search1 != $search2) return "mismatched attributes";

        // check all namespaces
        $ns1 = array();
        $ns2 = array();
        foreach ($xml1->getNamespaces() as $a => $b) {
            $ns1[$a] = "$b";
        }
        foreach ($xml2->getNamespaces() as $a => $b) {
            $ns2[$a] = "$b";
        }
        if ($ns1 != $ns2) return "mismatched namespaces";

        // get all namespace attributes
        foreach ($ns1 as $ns) {			// don't need to cycle over ns2, since its identical to ns1
            $search1 = array();
            $search2 = array();
            foreach ($xml1->attributes($ns) as $a => $b) {
                $search1[$a] = "$b";
            }
            foreach ($xml2->attributes($ns) as $a => $b) {
                $search2[$a] = "$b";
            }
            if ($search1 != $search2) return "mismatched ns:$ns attributes";
        }

        // get all children
        $search1 = array();
        $search2 = array();
        foreach ($xml1->children() as $b) {
            if (!isset($search1[$b->getName()]))
                $search1[$b->getName()] = array();
            $search1[$b->getName()][] = $b;
        }
        foreach ($xml2->children() as $b) {
            if (!isset($search2[$b->getName()]))
                $search2[$b->getName()] = array();
            $search2[$b->getName()][] = $b;
        }
        // cycle over children
        if (count($search1) != count($search2)) return "mismatched children count";		// xml2 has less or more children names (we don't have to search through xml2's children too)
        foreach ($search1 as $child_name => $children) {
            if (!isset($search2[$child_name])) return "xml2 does not have child $child_name";		// xml2 has none of this child
            if (count($search1[$child_name]) != count($search2[$child_name])) return "mismatched $child_name children count";		// xml2 has less or more children
            foreach ($children as $child) {
                // do any of search2 children match?
                $found_match = false;
                $reasons = array();
                foreach ($search2[$child_name] as $id => $second_child) {
                    if (($r = xml_is_equal($child, $second_child)) === true) {
                        // found a match: delete second
                        $found_match = true;
                        unset($search2[$child_name][$id]);
                    } else {
                        $reasons[] = $r;
                    }
                }
                if (!$found_match) return "xml2 does not have specific $child_name child: " . implode("; ", $reasons);
            }
        }

        // finally, cycle over namespaced children
        foreach ($ns1 as $ns) {			// don't need to cycle over ns2, since its identical to ns1
            // get all children
            $search1 = array();
            $search2 = array();
            foreach ($xml1->children() as $b) {
                if (!isset($search1[$b->getName()]))
                    $search1[$b->getName()] = array();
                $search1[$b->getName()][] = $b;
            }
            foreach ($xml2->children() as $b) {
                if (!isset($search2[$b->getName()]))
                    $search2[$b->getName()] = array();
                $search2[$b->getName()][] = $b;
            }
            // cycle over children
            if (count($search1) != count($search2)) return "mismatched ns:$ns children count";		// xml2 has less or more children names (we don't have to search through xml2's children too)
            foreach ($search1 as $child_name => $children) {
                if (!isset($search2[$child_name])) return "xml2 does not have ns:$ns child $child_name";		// xml2 has none of this child
                if (count($search1[$child_name]) != count($search2[$child_name])) return "mismatched ns:$ns $child_name children count";		// xml2 has less or more children
                foreach ($children as $child) {
                    // do any of search2 children match?
                    $found_match = false;
                    foreach ($search2[$child_name] as $id => $second_child) {
                        if (xml_is_equal($child, $second_child) === true) {
                            // found a match: delete second
                            $found_match = true;
                            unset($search2[$child_name][$id]);
                        }
                    }
                    if (!$found_match) return "xml2 does not have specific ns:$ns $child_name child";
                }
            }
        }

        // if we've got through all of THIS, then we can say that xml1 has the same attributes and children as xml2.
        return true;
    }

}
