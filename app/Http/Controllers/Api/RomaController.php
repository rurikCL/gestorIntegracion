<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiSolicitudCollection;
use App\Http\Resources\ApiSolicitudResource;
use App\Jobs\OrquestadorApi;
use App\Models\Api\ApiAutenticaciones;
use App\Models\Api\ApiProveedores;
use App\Models\Api\ApiSolicitudes;
use App\Models\FLU\FLU_Flujos;
use App\Models\FLU\FLU_Notificaciones;
use Carbon\Carbon;
use http\Env\Response;
use http\Params;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Mockery\Exception;
use SimpleXMLElement;
use SoapClient;
use SoapHeader;

class RomaController extends Controller
{
    public function index()
    {

        $controlador = new ApiSolicitudController();

        $respuesta = $controlador->urlCallParam(
            'https://roma.pompeyo.cl/respaldo/htmlv1/php/controller/controller.ventas.php'
            , 'POST'
            , [
                'FILTRO_date_start' => '2025-04-01',
                'FILTRO_date_end' => '2025-04-30',
                'FILTRO_unidad_negocio' => 'null',
                'FILTRO_gerencia' => 'null',
                'FILTRO_sucursal' => 'null',
                'FILTRO_vendedor' => 'null',
                'FILTRO_canal' => 'null',
                'FILTRO_origen' => 'null',
                'FILTRO_subOrigen' => 'null',
                'FILTRO_estado_ventas' => '4',
                'FILTRO_solicitudes' => 'null',
                'FILTRO_vpp' => 'null',
                'FILTRO_tasacion' => 'null',
                'FILTRO_revision' => 'null',
                'FILTRO_acta_engtrega' => 'null',
                'FILTRO_accesorios' => 'null',
                'FILTRO_tipo_accesorios' => 'null',
                'FILTRO_nota_credito' => 'null',
                'FILTRO_origen_cpd' => 'null',
                'FILTRO_marca' => 'null',
                'FILTRO_modelo' => 'null',
                'FILTRO_tareas' => 'null',
                'FILTRO_flotas' => '0',
                'FILTRO_manual' => 'null',
                'BTN_fecha_estimada' => '0',
                'usuario' => '2775',
                'action' => 'ListarRegistros',
            ]
        );

        return response($respuesta);
    }


}
