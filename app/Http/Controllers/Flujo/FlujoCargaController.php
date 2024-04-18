<?php

namespace App\Http\Controllers\Flujo;

use App\Http\Controllers\Controller;
use App\Imports\AutoredTransactionImport;
use App\Imports\CotizacionesForumImport;
use App\Imports\CotizacionesNissanImport;
use App\Imports\MK_LeadsImport;
use App\Imports\SalvinsImport;
use App\Models\FLU\FLU_Cargas;
use App\Models\TDP\TDP_Cotizaciones;
use App\Models\VT\VT_Salvin;
use http\Env\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class FlujoCargaController extends Controller
{

    public static function importCotizaciones($data)
    {
        $fileName = str_replace('"', '', $data["File"]);
        $resultado = [];
        $carga = FLU_Cargas::where('FechaCarga', $data["FechaCarga"])
            ->where('ID_Flujo', $data["ID_Flujo"])->first();

        /*if ($fileName && str_contains("nissan", $fileName)) {
            Log::info("Ejecutando flujo nissan");

            Excel::queueImport(new CotizacionesNissanImport($carga), "/public/" . $fileName, null, \Maatwebsite\Excel\Excel::CSV);
        } else if ($fileName && str_contains("forum", $fileName)){
            Log::info("Ejecutando flujo forum");

            Excel::queueImport(new CotizacionesForumImport($carga), "/public/" . $fileName, null, \Maatwebsite\Excel\Excel::CSV);
        }*/

        Log::info("Ejecutando flujo forum");

        TDP_Cotizaciones::truncate();

        Excel::queueImport(new CotizacionesForumImport($carga), "/public/" . $fileName, null, \Maatwebsite\Excel\Excel::CSV);

        $carga->fresh();
        $resultado = [
            "errores" => $carga->RegistrosFallidos,
            "registros" => $carga->RegistrosCargados
        ];


        return $resultado;
    }

    public static function importLeads($data)
    {
        $fileName = str_replace('"', '', $data["File"]);
        $resultado = [];
        $carga = FLU_Cargas::where('FechaCarga', $data["FechaCarga"])
            ->where('ID_Flujo', $data["ID_Flujo"])->first();

        if ($fileName) {
            Excel::queueImport(new MK_LeadsImport($carga, 'USADOS'), "/public/" . $fileName, null, \Maatwebsite\Excel\Excel::CSV);
        }

        $carga->fresh();
        $resultado = [
            "errores" => $carga->RegistrosFallidos,
            "registros" => $carga->RegistrosCargados
        ];
    }

    public static function importSalvins($data)
    {
        $fileName = str_replace('"', '', $data["File"]);
        $resultado = [];
        $carga = FLU_Cargas::where('FechaCarga', $data["FechaCarga"])
            ->where('ID_Flujo', $data["ID_Flujo"])->first();

        if ($fileName) {
            Excel::import(new SalvinsImport($carga), "/public/" . $fileName, null);
        }

        $carga->fresh();
        $resultado = [
            "errores" => $carga->RegistrosFallidos,
            "registros" => $carga->RegistrosCargados
        ];

        Log::info("Resultado : " . print_r($resultado, true));
        return $resultado;
    }

    public static function importTransactionAutored($data): array
    {
        $resultado = [];

        $fileName = str_replace('"', '', $data["File"]);
        $carga = FLU_Cargas::where('FechaCarga', $data["FechaCarga"])
            ->where('ID_Flujo', $data["ID_Flujo"])->first();

        try {
            if ($fileName) {
                $import = new AutoredTransactionImport($carga);
                $import->setFlujoID($data["ID_Flujo"]);

                $import->import("/public/" . $fileName, null, \Maatwebsite\Excel\Excel::CSV);
            }
            $carga->fresh();
            $carga->RegistrosFallidos = count($import->failures());
            $carga->save();

            $resultado = [
                "errores" => $carga->RegistrosFallidos,
                "registros" => $carga->RegistrosCargados
            ];

            Log::info("Resultado : " . print_r($resultado, true));

        } catch (\Exception $e) {
            Log::error("Error al importar transacciones autored : " . $e->getMessage());
            $carga->Estado = 1;
            $carga->save();
        }

        return $resultado;

    }


}
