<?php

namespace App\Http\Controllers;

use DateTime;
use DOMDocument;
use DOMXPath;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Admin\Entities\Config;
use Modules\Admin\Entities\Consumos;
use Modules\Admin\Entities\Contratos;
use Modules\Admin\Entities\Movimientos;
use Modules\Admin\Entities\Trabajadores;
use mysql_xdevapi\Exception;
use function Modules\Robots\Http\Controllers\newLog;

class RobotController extends Controller
{

    private $url;
    private $base;
    private $viewstate;
    private $viewstategenerator;
    private $eventvalidation;
    private $cookie;


    public function __invoke()
    {
        // TODO: Implement __invoke() method.
        $this->setCookie();
    }

    public function setUrl($url)
    {
        $this->url = $this->base . '/' . $url;
        return true;
    }

    public function setBase($url)
    {
        $this->base = $url;
        return true;
    }

    public function setCookie()
    {
        $this->cookie = "./storage/app/cookiefile.txt";
    }

    public function login($loginData)
    {
        print("INICIO LOGIN " . PHP_EOL);

        $retorno = [
            'status' => 'ERROR',
            'msj' => 'Ocurrio un error',
            'data' => ''
        ];

        if ($this->url) {
            try {
                $this->setCookie();

                // Obtenemos valores de configuracion
                $configUser = $loginData["user"];
                $configPass = $loginData["pass"];

                // Primer llamado, obtener variables de entrada
                $output = $this->get_site_html($this->base . '/PUB/login.aspx');

                $data = [];
                if ($output) {
                    preg_match('/<input.*?id="__VIEWSTATE".*? value="(.*?)".*?\/>/s', $output, $this->viewstate);
                    preg_match('/<input.*?id="__VIEWSTATEGENERATOR".*? value="(.*?)".*?\/>/s', $output, $this->viewstategenerator);
                    preg_match('/<input.*?id="__EVENTVALIDATION".*? value="(.*?)".*?\/>/s', $output, $this->eventvalidation);

                    if (count($this->viewstate) > 1)
                        $data = '__EVENTTARGET=&__EVENTARGUMENT=&__VIEWSTATE=' . urlencode($this->viewstate[1])
                            . '&__VIEWSTATEGENERATOR=' . urlencode($this->viewstategenerator[1])
                            . '&__EVENTVALIDATION=' . urlencode($this->eventvalidation[1])
                            . '&ctl00%24MainContent%24txtUsername=' . $configUser
                            . '&ctl00%24MainContent%24txtPassword=' . $configPass
                            . '&ctl00%24MainContent%24btnLogin2=Ingresar';
                }


                // Segundo llamado, gatillando Login
                $output = $this->get_site_html($this->base . '/PUB/login.aspx', $data);

                if ($output) {
                    $retorno['status'] = 'OK';
                    $retorno['msj'] = 'Login correcto';

//                    $output = $this->get_site_html($this->base.'/SCA/PnlAdministradorProveedor.aspx');
//                    dd($output);
                }

            } catch (Exception $e) {
                dd($e->getMessage());
            }

        } else {
            $retorno['msj'] = 'No se pudo establecer el login';
        }

        return $retorno;
    }


    public function scrapContratos()
    {
        print("INICIO SCRAP CONTRATOS " . PHP_EOL);
        newLog("Contratos", "INFO", "Inicio Scraping Contratos");

        $output = $this->get_site_html($this->base . '/SCA/PnlAdministradorProveedor.aspx');
        /*        preg_match('/<table.*?id="MainContent_tabDatos".*?>(.*?)<\/table>/sm', $output, $tablaContratos);*/

        $data = $this->getDomDataTable($output, 'MainContent_tabDatos');

        $contador = 0;

        try {
            foreach ($data as $contador => $registro) {
                if ($contador) {
                    $fecha = explode("/", $registro[5]);
                    $fechaContrato = $fecha[2] . "-" . $fecha[1] . "-" . $fecha[0];

                    $fecha = explode("/", $registro[6]);
                    $fechaFinContrato = $fecha[2] . "-" . $fecha[1] . "-" . $fecha[0];

                    $fecha = explode(" ", $registro[7]);
                    $fecha2 = explode("/", $fecha[0]);
                    $fechaAcreditacion = $fecha2[2] . "-" . $fecha2[1] . "-" . $fecha2[0] . " " . $fecha[1] . ":00";

                    $contrato = Contratos::firstOrNew(
                        ["FTF03_codigo_contrato" => $registro[1]],
                        [
                            "FTF03_codigo_contrato" => $registro[1],
                            "FTF03_descripcion" => $registro[2],
                            "FTF03_compania" => $registro[3],
                            "FTF03_admin_contrato" => $registro[4],
                            "FTF03_inicio_contrato" => $fechaContrato,
                            "FTF03_fin_contrato" => $fechaFinContrato,
                            "FTF03_acreditacion" => $fechaAcreditacion,
                            "FTF03_url_data_contratos" => $registro[8],

                        ]
                    );

                    $urlContrato = $registro[8];
                    $output = $this->get_site_html($this->base . '/SCA/' . $urlContrato);

                    $data = $this->getDomData($output, 'a', 'MainContent_HypTrabajador');
                    $urlTrabajadores = $data->item(0)->getAttribute('href');

                    $contrato->FTF03_url_data_contratos = $registro[8];
                    $contrato->FTF03_url_data_trabajadores = $urlTrabajadores;

                    $contrato->save();
                }
            }
            newLog("Contratos", "INFO", $contador . " contratos guardados");

        } catch (Exception $e) {
            newLog("Contratos", "ERROR", "Error en scraping contratos", $e->getMessage());
        }

        return $data;
    }

    public function scrapTrabajadores($contratoID = 0)
    {
        print("INICIO SCRAP TRABAJADORES " . PHP_EOL);
        newLog("Trabajadores", "INFO", "Inicio Scraping Trabajadores");

        $dom = new DOMDocument;
        libxml_use_internal_errors(true);

        try {
            $contratos = Contratos::get();
            foreach ($contratos as $contrato) {
                $urlContrato = $contrato->FTF03_url_data_contratos;
                $urlTrabajadores = $contrato->FTF03_url_data_trabajadores;

                $output = $this->get_site_html($this->base . '/SCA/' . $urlTrabajadores);
                $data = $this->getDomDataTable($output, 'MainContent_tabDatos');

                $dom->loadHTML($output);
                libxml_clear_errors();
                // Creamos el objeto Xpath para buscar objetos dentro del HTML
                $xpath = new DOMXpath($dom);

                // Buscamos si existen mas de 50 registros, y hay que paginar los datos ---------------------------------
                if ($dom->getElementById("MainContent_lblCount") != null) {
                    $contadorRegistros = $dom->getElementById("MainContent_lblCount")->nodeValue;
                } else {
                    $contadorRegistros = 1;
                }
                $contadorCiclo = ceil($contadorRegistros / 50);
                print("Registros encontrados : " . $contadorRegistros . " en " . $contadorCiclo . " paginas " . PHP_EOL);
                $contadorGuardados = 0;


                // Iniciamos ciclo por cantidad de paginas encontradas --------------------------------------------------
                for ($i = 0; $i <= $contadorCiclo; $i++) {

                    // Si existe mas de una pagina
                    if ($contadorCiclo > 1) {
                        libxml_clear_errors();
                        $xpath = new DOMXpath($dom);

                        // Buscamos el ultimo enlace de paginacion
                        $urlNext = $xpath->query("(//span[@id='MainContent_lblPages']/a[@class='LinkDetalle'])[last()]");
                        $urlNext = $urlNext->item(0)->getAttribute("href");
//                    print($urlNext . PHP_EOL);

                        // Solicitamos el GET de la URL de paginacion
                        list($header, $output, $info) = $this->getUrlDataInfo($this->base . '/SCA/' . $urlNext);

                        // Cargamos el HTML en el DOM
                        $dom->loadHTML($output);

                        // Genaramos la tabla en Array para los datos
                        $data = $this->getDomDataTable($output, 'MainContent_tabDatos');
                        //            print_r($data);
                    }

                    $contador = 0;

                    foreach ($data as $contador => $registro) {
                        if ($contador) {
                            $trabajador = Trabajadores::firstOrNew(
                                [
                                    "FTF04_rut" => $registro[2],
                                    "FTF03_id" => $contrato->FTF03_id
                                ],
                                [
                                    "FTF04_contratista" => $registro[1],
                                    "FTF04_rut" => ltrim($registro[2], "0"),
                                    "FTF04_nombre" => $registro[3],
                                    "FTF04_cargo" => $registro[4],
                                    "FTF04_turno" => $registro[5],
                                    "FTF04_estado_trabajador" => $registro[6],
                                    "FTF04_url_data_detalles" => $registro[7],
                                    "FTF03_id" => $contrato->FTF03_id
                                ]
                            );

                            $trabajador->FTF04_status_movimientos = 0;
                            $trabajador->FTF04_status_consumo = 0;

                            $trabajador->save();
                        }
                    }
                    newLog("Trabajadores", "INFO", $contador . " trabajadores guardados");
                }

            }
        } catch (Exception $e) {
            newLog("Trabajadores", "ERROR", "Error en scraping trabajadores", $e->getMessage());
        }
    }

    public function scrapMovimientos($trabajadorRut)
    {
//        print("INICIO SCRAP MOVIMIENTOS " . PHP_EOL);
        newLog("Movimientos", "INFO", "Inicio Scraping Movimientos");

        $dom = new DOMDocument;
        libxml_use_internal_errors(true);

        if ($trabajadorRut) {
            $trabajadores = Trabajadores::where('FTF04_rut', $trabajadorRut)->get();
        } else {
            $trabajadores = Trabajadores::getAll();
        }

        foreach ($trabajadores as $trabajador) {

            try {

                print("Extrayendo data de rut : " . $trabajador->FTF04_rut . PHP_EOL);

                $trabajadorUrl = $trabajador->FTF04_url_data_detalles;

                // Ingresamos a pagina del trabajador, y obtenemos datos de cabeceras para el POST
                $response = $this->get_site_html($this->base . '/SCA/' . $trabajadorUrl);
                $dom->loadHTML($response);
                libxml_clear_errors();

                // Creamos el objeto Xpath para buscar objetos dentro del HTML
                $xpath = new DOMXpath($dom);

                // Definimos los parametros a buscar por medio de Regex + Xpath  ----------------------------------------
                preg_match('/<input.*?id="__VIEWSTATE".*? value="(.*?)".*?\/>/s', $response, $viewstate);
                preg_match('/<input.*?id="__VIEWSTATEGENERATOR".*? value="(.*?)".*?\/>/s', $response, $viewstategenerator);
                preg_match('/<input.*?id="__EVENTVALIDATION".*? value="(.*?)".*?\/>/s', $response, $eventvalidation);

                $contadorGuardados = 0;

                if ($dom->getElementById('MainContent_txtIdTrabajador')) {

                    $idtrabajador = $dom->getElementById('MainContent_txtIdTrabajador')->getAttribute('value');
                    $empresa = $dom->getElementById('MainContent_txtEmpresa')->getAttribute('value');

                    $cargo = $xpath->query('//select[@id="MainContent_SelIdCargo"]/option[attribute::selected]');
                    $estado = $xpath->query('//select[@id="MainContent_selEstadoTrabajador"]/option[attribute::selected]');
                    $acreditacion = $dom->getElementById('MainContent_txtIdEstadoAcreditacion')->getAttribute('value');
                    $turno = $xpath->query('//select[@id="MainContent_SelIdTurno"]/option[attribute::selected]');
                    $jornada = $xpath->query('//select[@id="MainContent_SelIdJornada"]/option[attribute::selected]');
                    $primerdia = $dom->getElementById('MainContent_txtPrimerDiaDeTrabajo')->getAttribute('value');
                    $pernocta = $xpath->query('//select[@id="MainContent_SelIdPernocta"]/option[attribute::selected]');
                    $eventtarget = 'ctl00$MainContent$btnMovimiento';
                    $eventargumnent = '';

                    // Preparamos el payload para el POST con los datos obtenidos -------------------------------------------
                    $fields = array(
                        '__EVENTTARGET' => $eventtarget,
                        '__EVENTARGUMENT' => ($eventargumnent),
                        '__VIEWSTATE' => ($viewstate[1]),
                        '__VIEWSTATEGENERATOR' => ($viewstategenerator[1]),
                        '__EVENTVALIDATION' => ($eventvalidation[1]),
                        'ctl00$MainContent$txtIdTrabajador' => ($idtrabajador),
                        'ctl00$MainContent$txtEmpresa' => ($empresa),
                        'ctl00$MainContent$SelIdCargo' => ($cargo->item(0)->nodeValue),
                        'ctl00$MainContent$selEstadoTrabajador' => ($estado->item(0)->nodeValue),
                        'ctl00$MainContent$txtIdEstadoAcreditacion' => ($acreditacion),
                        'ctl00$MainContent$SelIdTurno' => ($turno->item(0)->nodeValue),
                        'ctl00$MainContent$SelIdJornada' => ($jornada->count()) ? $jornada->item(0)->nodeValue : '',
                        'ctl00$MainContent$txtPrimerDiaDeTrabajo' => ($primerdia),
                        'ctl00$MainContent$SelIdPernocta' => ($pernocta->item(0)->nodeValue)
                    );

                    $ultimoDiaMovimiento = Movimientos::where('FTF05_rut', $trabajadorRut)
                        ->orderBy('FTF05_fechahora', 'DESC')
                        ->first();
                    if ($ultimoDiaMovimiento) $ultimoDiaMovimiento = $ultimoDiaMovimiento->FTF05_fechahora;
                    else $ultimoDiaMovimiento = '0000-00-00 00:00:00';

                    print_r("Ultimo dia Movimiento : " . $ultimoDiaMovimiento . PHP_EOL);

                    $urlFields = http_build_query($fields);

                    // Ejecutamos POST y generamos la tabla de datos en Array -----------------------------------------------
                    list($header, $output, $info) = $this->getUrlDataInfo($this->base . '/SCA/' . $trabajadorUrl, $urlFields);
                    $data = $this->getDomDataTable($output, 'MainContent_tabDatos');
//            print_r($data);

                    // Cargamos el HTML dentro del DOM
                    $dom->loadHTML($output);

                    // Buscamos si existen mas de 50 registros, y hay que paginar los datos ---------------------------------
                    $contadorRegistros = $dom->getElementById("MainContent_lblCount")->nodeValue;
                    $contadorCiclo = ceil($contadorRegistros / 50);

                    print("Registros encontrados : " . $contadorRegistros . " en " . $contadorCiclo . " paginas " . PHP_EOL);

                    // Iniciamos ciclo por cantidad de paginas encontradas --------------------------------------------------
                    for ($i = 0; $i <= $contadorCiclo; $i++) {

                        // Si existe mas de una pagina
                        if ($contadorCiclo > 1) {
                            libxml_clear_errors();
                            $xpath = new DOMXpath($dom);

                            // Buscamos el ultimo enlace de paginacion
                            $urlNext = $xpath->query("(//span[@id='MainContent_lblPages']/a[@class='LinkDetalle'])[last()]");
                            $urlNext = $urlNext->item(0)->getAttribute("href");
//                    print($urlNext . PHP_EOL);

                            // Solicitamos el GET de la URL de paginacion
                            list($header, $output, $info) = $this->getUrlDataInfo($this->base . '/SCA/' . $urlNext);

                            // Cargamos el HTML en el DOM
                            $dom->loadHTML($output);

                            // Genaramos la tabla en Array para los datos
                            $data = $this->getDomDataTable($output, 'MainContent_tabDatos');
                            //            print_r($data);
                        }

                        foreach ($data as $count => $registro) {
                            if ($count) {
                                $fecha = explode(" ", $registro[2]);
                                $fecha2 = explode("/", $fecha[0]);
                                $fechaHora = $fecha2[2] . "-" . $fecha2[1] . "-" . $fecha2[0] . " " . $fecha[1] . ":00";
                                $fecha = $fecha2[2] . "-" . $fecha2[1] . "-" . $fecha2[0];

                                if ($ultimoDiaMovimiento != '' && strtotime($fechaHora) > strtotime($ultimoDiaMovimiento)) {

                                    $movimiento = Movimientos::firstOrNew(
                                        [
                                            "FTF04_id" => $trabajador->FTF04_id,
                                            "FTF05_rut" => ltrim($trabajador->FTF04_rut, "0"),
                                            "FTF05_compania" => $registro[1],
                                            "FTF05_fechahora" => $fechaHora,
                                            "FTF05_area" => $registro[3],
                                            "FTF05_tipo" => $registro[4],
                                            "FTF05_contrato" => $registro[6]
                                        ],
                                        [
                                            "FTF04_id" => $trabajador->FTF04_id,
                                            "FTF05_rut" => ltrim($trabajador->FTF04_rut, "0"),
                                            "FTF05_compania" => $registro[1],
                                            "FTF05_fechahora" => $fechaHora,
                                            "FTF05_fecha" => $fecha,
                                            "FTF05_area" => $registro[3],
                                            "FTF05_tipo" => $registro[4],
                                            "FTF05_acompanante" => $registro[5],
                                            "FTF05_contrato" => $registro[6],
                                            "FTF05_descripcion" => $registro[7],
                                            "FTF05_tipoacreditacion" => $registro[8],
                                            "FTF05_status" => 0,
                                        ]
                                    );
                                    print(".");

                                    if ($movimiento->save()) $contadorGuardados++;

                                } else {
                                    print("Se extrajo la informacion reciente. cortando ciclo" . PHP_EOL);
                                    return true;
                                }
                            }
                        }
                    }
                }
                print(PHP_EOL . "Registros guardados : " . $contadorGuardados . PHP_EOL);
                newLog("Movimientos", "INFO", "Registros guardados : " . $contadorGuardados);


            } catch (\Exception $e) {
                print_r($e->getMessage());
                newLog("Movimientos", "ERROR", "Error en scraping movimientos", $e->getMessage());

                return false;
            }

        }

        return true;
    }

    public function scrapConsumos($trabajadorRut)
    {
//        print("INICIO SCRAP CONSUMOS " . PHP_EOL);
        newLog("Consumos", "INFO", "Inicio Scraping Consumos");

        $dom = new DOMDocument;
        libxml_use_internal_errors(true);


        if ($trabajadorRut) {
            $trabajadores = Trabajadores::where('FTF04_rut', $trabajadorRut)->get();
        } else {
            $trabajadores = Trabajadores::getAll();
        }

        foreach ($trabajadores as $trabajador) {
            try {

                print("Extrayendo data de rut : " . $trabajador->FTF04_rut . PHP_EOL);

                $trabajadorUrl = $trabajador->FTF04_url_data_detalles;

                // Ingresamos a pagina del trabajador, y obtenemos datos de cabeceras para el POST
                $response = $this->get_site_html($this->base . '/SCA/' . $trabajadorUrl);
                $dom->loadHTML($response);
                libxml_clear_errors();

                // Creamos el objeto Xpath para buscar objetos dentro del HTML
                $xpath = new DOMXpath($dom);

                // Definimos los parametros a buscar por medio de Regex + Xpath  ----------------------------------------
                preg_match('/<input.*?id="__VIEWSTATE".*? value="(.*?)".*?\/>/s', $response, $viewstate);
                preg_match('/<input.*?id="__VIEWSTATEGENERATOR".*? value="(.*?)".*?\/>/s', $response, $viewstategenerator);
                preg_match('/<input.*?id="__EVENTVALIDATION".*? value="(.*?)".*?\/>/s', $response, $eventvalidation);

                $contadorGuardados = 0;

                if ($dom->getElementById('MainContent_txtIdTrabajador')) {
                    $idtrabajador = $dom->getElementById('MainContent_txtIdTrabajador')->getAttribute('value');
                    $empresa = $dom->getElementById('MainContent_txtEmpresa')->getAttribute('value');

                    $cargo = $xpath->query('//select[@id="MainContent_SelIdCargo"]/option[attribute::selected]');
                    $estado = $xpath->query('//select[@id="MainContent_selEstadoTrabajador"]/option[attribute::selected]');
                    $acreditacion = $dom->getElementById('MainContent_txtIdEstadoAcreditacion')->getAttribute('value');
                    $turno = $xpath->query('//select[@id="MainContent_SelIdTurno"]/option[attribute::selected]');
                    $jornada = $xpath->query('//select[@id="MainContent_SelIdJornada"]/option[attribute::selected]');
                    $primerdia = $dom->getElementById('MainContent_txtPrimerDiaDeTrabajo')->getAttribute('value');
                    $pernocta = $xpath->query('//select[@id="MainContent_SelIdPernocta"]/option[attribute::selected]');
                    $eventtarget = 'ctl00$MainContent$btnConsumo';
                    $eventargumnent = '';

                    // Preparamos el payload para el POST con los datos obtenidos -------------------------------------------
                    $fields = array(
                        '__EVENTTARGET' => $eventtarget,
                        '__EVENTARGUMENT' => ($eventargumnent),
                        '__VIEWSTATE' => ($viewstate[1]),
                        '__VIEWSTATEGENERATOR' => ($viewstategenerator[1]),
                        '__EVENTVALIDATION' => ($eventvalidation[1]),
                        'ctl00$MainContent$txtIdTrabajador' => ($idtrabajador),
                        'ctl00$MainContent$txtEmpresa' => ($empresa),
                        'ctl00$MainContent$SelIdCargo' => ($cargo->item(0)->nodeValue),
                        'ctl00$MainContent$selEstadoTrabajador' => ($estado->item(0)->nodeValue),
                        'ctl00$MainContent$txtIdEstadoAcreditacion' => ($acreditacion),
                        'ctl00$MainContent$SelIdTurno' => ($turno->item(0)->nodeValue),
                        'ctl00$MainContent$SelIdJornada' => ($jornada->count()) ? $jornada->item(0)->nodeValue : '',
                        'ctl00$MainContent$txtPrimerDiaDeTrabajo' => ($primerdia),
                        'ctl00$MainContent$SelIdPernocta' => ($pernocta->item(0)->nodeValue)
                    );

                    $ultimoDiaConsumo = Consumos::where('FTF06_rut', $trabajadorRut)
                        ->orderBy('FTF06_fechahora', 'DESC')
                        ->first();
                    if ($ultimoDiaConsumo) $ultimoDiaConsumo = $ultimoDiaConsumo->FTF06_fechahora;
                    else $ultimoDiaConsumo = '0000-00-00 00:00:00';

                    print_r("Ultimo dia Movimiento : " . $ultimoDiaConsumo . PHP_EOL);

                    $urlFields = http_build_query($fields);

                    // Ejecutamos POST y generamos la tabla de datos en Array -----------------------------------------------
                    list($header, $output, $info) = $this->getUrlDataInfo($this->base . '/SCA/' . $trabajadorUrl, $urlFields);
                    $data = $this->getDomDataTable($output, 'MainContent_tabDatos');

                    // Cargamos el HTML dentro del DOM
                    $dom->loadHTML($output);

                    // Buscamos si existen mas de 50 registros, y hay que paginar los datos ---------------------------------
                    $contadorRegistros = $dom->getElementById("MainContent_lblCount")->nodeValue;
                    $contadorCiclo = ceil($contadorRegistros / 50);
                    print("Registros encontrados : " . $contadorRegistros . " en " . $contadorCiclo . " paginas " . PHP_EOL);

                    // Iniciamos ciclo por cantidad de paginas encontradas --------------------------------------------------
                    for ($i = 0; $i <= $contadorCiclo; $i++) {

                        // Si existe mas de una pagina
                        if ($contadorCiclo > 1) {
                            libxml_clear_errors();
                            $xpath = new DOMXpath($dom);

                            // Buscamos el ultimo enlace de paginacion
                            $urlNext = $xpath->query("(//span[@id='MainContent_lblPages']/a[@class='LinkDetalle'])[last()]");
                            $urlNext = $urlNext->item(0)->getAttribute("href");
//                    print($urlNext . PHP_EOL);

                            // Solicitamos el GET de la URL de paginacion
                            list($header, $output, $info) = $this->getUrlDataInfo($this->base . '/SCA/' . $urlNext);

                            // Cargamos el HTML en el DOM
                            $dom->loadHTML($output);

                            // Genaramos la tabla en Array para los datos
                            $data = $this->getDomDataTable($output, 'MainContent_tabDatos');
                            //            print_r($data);
                        }

                        // Iniciamos el ciclo por los registros de la tabla, para guardarlos -------------------------------
                        foreach ($data as $count => $registro) {
                            if ($count) {
                                $fecha = explode(" ", $registro[2]);
                                $fecha2 = explode("/", $fecha[0]);
                                $fechaHora = $fecha2[2] . "-" . $fecha2[1] . "-" . $fecha2[0] . " " . $fecha[1] . ":00";
                                $fecha = $fecha2[2] . "-" . $fecha2[1] . "-" . $fecha2[0];

                                if ($ultimoDiaConsumo != '' && strtotime($fechaHora) > strtotime($ultimoDiaConsumo)) {

                                    $consumo = Consumos::firstOrNew(
                                        [
                                            "FTF04_id" => $trabajador->FTF04_id,
                                            "FTF06_rut" => $trabajador->FTF04_rut,
                                            "FTF06_compania" => $registro[1],
                                            "FTF06_fechahora" => $fechaHora,
                                            "FTF06_fecha" => $fecha,
                                            "FTF06_tipo_consumo" => $registro[3],
                                            "FTF06_area" => $registro[4],
                                            "FTF06_tipo_acreditacion" => $registro[5],
                                            "FTF06_servicio" => $registro[6],
                                            "FTF06_responsable" => $registro[7],
                                            "FTF06_status" => 0,
                                        ]
                                    );
                                    print(".");

                                    if ($consumo->save()) $contadorGuardados++;

                                } else {
                                    print("Se extrajo la informacion reciente. cortando ciclo" . PHP_EOL);
                                    return true;
                                }
                            }
                        }
                    }
                }
                print(PHP_EOL . "Registros guardados : " . $contadorGuardados . PHP_EOL);
                newLog("Consumos", "INFO", "Registros guardados : " . $contadorGuardados);

            } catch (\Exception $e) {
                print_r($e->getMessage());
                newLog("Consumos", "ERROR", "Error en scraping consumos", $e->getMessage());
                return false;
            }
        }

        return true;
    }


    public function get_site_html($site_url, $data = '')
    {

        $ch = curl_init($site_url);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 9);
//        curl_setopt($ch, CURLOPT_HEADER, true);
//        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36');
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        if ($data != '') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//            "Accept: text/html application/xhtml+xml, imagen/jxr, */*",
//            "Accept-Language: en-ES,en-MX;q=0.7,es,q=0.3",
//            "Accept-Encoding: gzip, deflate",
//            "Content-Type: application/x-www-form-urlencoded",
//            "Connection: keep-alive",
//            'Cache-Control: no-cache',
//            'Origin:' . $this->base,
//            'Referer:' . $site_url,
//        ));

        $response = curl_exec($ch);

        global $base_url;
        $base_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $http_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//        print($http_response_code . PHP_EOL);
//        print($http_response_code);

        curl_close($ch);
        return $response;
    }

    public function getDomDataTable($html, $tabla)
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        $xpath = new DOMXpath($dom);

        $data = array();

        // obtiene las filas de la tabla indicada
        $table_rows = $xpath->query('//table[@id="' . $tabla . '"]/tr');
        foreach ($table_rows as $row => $tr) {

            foreach ($tr->childNodes as $count => $td) {
                $data[$row][$count] = preg_replace('~[\r\n]+~', '', trim($td->textContent));
                if ($td->textContent == 0) $data[$row][$count] = '0';

                // Si el campo contiene un enlace
                $subElement = $xpath->query("a[contains(@class,'LinkDetalle')]", $td);
                if ($subElement->item(0)) {
                    $data[$row][$count] = $subElement->item(0)->getAttribute('href');
                }
            }
            $data[$row] = array_values(($data[$row]));
        }
        return $data;
    }

    public function getDomData($html, $element, $id)
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        $xpath = new DOMXpath($dom);

        $data = $xpath->query('//' . $element . '[@id="' . $id . '"]');

        return $data;

    }

    function getUrlDataInfo(string $url, string $params = null, $nobody = null): array
    {
        $cookie = $this->cookie;

        $curlOptions = array(
            CURLOPT_COOKIEJAR => $cookie,
            CURLOPT_COOKIEFILE => $cookie,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HEADER => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36',
            CURLOPT_ENCODING => 'gzip,deflate',
            CURLOPT_CONNECTTIMEOUT => 128,
            CURLOPT_TIMEOUT => 128,
            CURLOPT_URL => $url,
            CURLOPT_MAXREDIRS => 9
        );

        if ($params != null) {
            $curlOptions[CURLOPT_POST] = true;
            $curlOptions[CURLOPT_POSTFIELDS] = $params;
        }
        if ($nobody != null) {
            $curlOptions[CURLOPT_NOBODY] = true;
        }

        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);

        $response = curl_exec($ch);

        $info = curl_getinfo($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = []; // ya no se requiere

        // extract body
        $body = substr($response, $headerSize);

        curl_close($ch);

        return [$header, $body, $info];
    }
}
