<?php

namespace App\Http\Controllers\Flujo;

use App\Http\Controllers\Controller;
use App\Imports\ApcInformeOtImport;
use App\Imports\ApcRentabilidadOtImport;
use App\Imports\ApcStockImport;
use App\Imports\AutoredTransactionImport;
use App\Imports\CotizacionesForumImport;
use App\Imports\CotizacionesNissanImport;
use App\Imports\FinancierasImport;
use App\Imports\MK_LeadsImport;
use App\Imports\SalvinsImport;
use App\Imports\SantanderImport;
use App\Models\APC_InformeOt;
use App\Models\APC_RentabilidadOt;
use App\Models\APC_Stock;
use App\Models\FLU\FLU_Cargas;
use App\Models\TDP\TDP_Cotizaciones;
use App\Models\VT\VT_CotizacionesSolicitudesCredito;
use App\Models\VT\VT_Salvin;
use http\Env\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

// Los metodos aqui presentes, se deben crear en apps3, Opcion Flujos, con tipo Carga y en opciones, especificar el Metodo a ejecutar

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
        Log::info("Inicio de importacion Salvin");
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

    public static function importSantanderDrive($data): array
    {
        $resultado = [];

        $fileName = str_replace('"', '', $data["File"]);
        $carga = FLU_Cargas::where('FechaCarga', $data["FechaCarga"])
            ->where('ID_Flujo', $data["ID_Flujo"])->first();

        try {
            if ($fileName) {
                $import = new SantanderImport($carga);
                $import->import("/public/" . $fileName, null, \Maatwebsite\Excel\Excel::XLSX);
            }
            $carga->fresh();
//            $carga->RegistrosFallidos = count($import->failures() ?? []);
//            $carga->save();

            $resultado = [
                "errores" => $carga->RegistrosFallidos,
                "registros" => $carga->RegistrosCargados
            ];

            Log::info("Resultado : " . print_r($resultado, true));

        } catch (\Exception $e) {
            Log::error("Error al importar solicitudes Santander : " . $e->getMessage());
            $carga->Estado = 1;
            $carga->save();
        }

        return $resultado;

    }

    public static function importRentabilidadOt($data)
    {
        Log::info("Inicio de importacion Rentabilidad OT");
        $fileName = str_replace('"', '', $data["File"]);
        $resultado = [];
        $carga = FLU_Cargas::where('FechaCarga', $data["FechaCarga"])
            ->where('ID_Flujo', $data["ID_Flujo"])->first();

        if ($fileName) {
            Excel::import(new ApcRentabilidadOtImport($carga), "/public/" . $fileName);
        }

        $carga->fresh();
        $resultado = [
            "errores" => $carga->RegistrosFallidos,
            "registros" => $carga->RegistrosCargados
        ];

        Log::info("Fin de importacion Rentabilidad OT");

//        Log::info("Resultado : " . print_r($resultado, true));
        return $resultado;
    }

    public static function importStock($data)
    {
        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        Log::info("Inicio de importacion Stock");
        $fileName = str_replace('"', '', $data["File"]);
        $resultado = [];
        $carga = FLU_Cargas::where('FechaCarga', $data["FechaCarga"])
            ->where('ID_Flujo', $data["ID_Flujo"])->first();

        if ($fileName) {
//            APC_Stock::truncate();
            Excel::import(new ApcStockImport($carga), "/public/" . $fileName);

            $carga->Estado = 2;
            $carga->save();
        }

        $carga->fresh();
        $resultado = [
            "errores" => $carga->RegistrosFallidos,
            "registros" => $carga->RegistrosCargados
        ];

        Log::info("Fin de importacion Stock");

//        Log::info("Resultado : " . print_r($resultado, true));
        return $resultado;
    }


    public static function importFinancieras($data): array
    {

        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        $resultado = [];

        $fileName = str_replace('"', '', $data["File"]);
        $carga = FLU_Cargas::where('FechaCarga', $data["FechaCarga"])
            ->where('ID_Flujo', $data["ID_Flujo"])->first();

        try {
            if ($fileName) {
//                VT_CotizacionesSolicitudesCredito::truncate();
                $import = new FinancierasImport($carga, $data["FechaDesde"], $data["FechaHasta"]);
                $import->import("/public/" . $fileName, null, \Maatwebsite\Excel\Excel::XLSX);
            }
            $carga->fresh();
//            $carga->RegistrosFallidos = count($import->failures() ?? []);
//            $carga->save();

            $resultado = [
                "errores" => $carga->RegistrosFallidos,
                "registros" => $carga->RegistrosCargados
            ];

//            Log::info("Resultado : " . print_r($resultado, true));

        } catch (\Exception $e) {
            Log::error("Error al importar financieras : " . $e->getMessage());
            $carga->Estado = 3;
            $carga->save();
        }

        return $resultado;

    }


    public static function importInformeOt($data)
    {
        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        $resultado = [];

        $fileName = str_replace('"', '', $data["File"]);
        $carga = FLU_Cargas::where('FechaCarga', $data["FechaCarga"])
            ->where('ID_Flujo', $data["ID_Flujo"])->first();

        try {
            if ($fileName) {
                $import = new ApcInformeOtImport($carga);
//                $import->import("/public/" . $fileName, null, \Maatwebsite\Excel\Excel::XLS);
                $res = Excel::import($import, storage_path('/app/public/' . $fileName), null, \Maatwebsite\Excel\Excel::XLS);

                dump($res->get());
                if ($res) {
                    // Actualiza el tramo de los registros
                    APC_InformeOt::UpdateTramo();

                    // paso final --------------------------------------------------
                    $carga->Estado = 2;
                    $carga->RegistrosCargados = $import->getRegistrosCargados();
                    $carga->RegistrosFallidos = $import->getRegistrosFallidos();
                    $carga->save();
                } else {
                    $carga->Estado = 3;
                    $carga->save();
                }

            }
            $carga->fresh();
//            $carga->RegistrosFallidos = count($import->failures() ?? []);
//            $carga->save();

            $resultado = [
                "errores" => $carga->RegistrosFallidos,
                "registros" => $carga->RegistrosCargados
            ];

            Log::info("Resultado : " . print_r($resultado, true));

        } catch (\Exception $e) {
            Log::error("Error al importar financieras : " . $e->getMessage());
            $carga->Estado = 1;
            $carga->save();
        }

        return $resultado;
    }

}
