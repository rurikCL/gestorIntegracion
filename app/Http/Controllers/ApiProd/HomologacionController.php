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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Exception;

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
class HomologacionController extends Controller
{

    public function getHomologacion(Request $request)
    {
        try {
            $homologacion = FLU_Homologacion::where('Estado', 'Activo')->get();
            return response()->json(['data' => $homologacion], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching homologation data: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function getHomologacionById($id)
    {
        try {
            $homologacion = FLU_Homologacion::find($id);
            if (!$homologacion) {
                return response()->json(['error' => 'Homologation not found'], 404);
            }
            return response()->json(['data' => $homologacion], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching homologation by ID: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function createHomologacion(Request $request)
    {
        try {
            $data = $request->validate([
                'Nombre' => 'required|string|max:255',
                'Descripcion' => 'nullable|string',
                'Estado' => 'required|in:Activo,Inactivo',
            ]);

            $homologacion = FLU_Homologacion::create($data);
            return response()->json(['data' => $homologacion], 201);
        } catch (\Exception $e) {
            Log::error('Error creating homologation: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}


