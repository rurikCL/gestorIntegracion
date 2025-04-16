<?php

namespace App\Imports;

use App\Models\APC_InformeOt;
use App\Models\APC_MovimientoVentas;
use App\Models\APC_RentabilidadOt;
use App\Models\APC_Repuestos;
use App\Models\APC_Sku;
use App\Models\APC_Stock;
use App\Models\FLU\FLU_Homologacion;
use App\Models\MA\MA_Marcas;
use App\Models\MA\MA_Sucursales;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Events\AfterImport;

class ApcInformeOtImport implements ToModel, WithBatchInserts, WithEvents, WithStartRow, WithUpserts
{

    use RegistersEventListeners;
    use Importable, SkipsFailures;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    private $carga = null;
    private $contadorRegistro = 0;
    private $contErrores = 0;
    private $errores = [];

    public function __construct($carga = null)
    {
        $this->carga = $carga;
    }
    public function model(array $row)
    {

        if ($this->carga) {
            $this->contadorRegistro = $this->carga->RegistrosCargados ?? 0;
            $contErrores = $this->carga->RegistrosFallidos ?? 0;
            $idCarga = $this->carga->ID;
        }
        $h = new FLU_Homologacion();
        $idFlujo = 30;
        $marca = explode(" ", $row[10]);
        $sucursal = MA_Sucursales::where('Sucursal', 'like', $row[0])->first();
        if($sucursal){
            $sucursal = $sucursal->ID;
        }else {
            $sucursal = $h->getDato($row[0] . $row[3], $idFlujo, 'sucursal', 0);
        }


        $fechaIngreso = Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[1]));
        $tramo = intval((Carbon::now()->diffInDays($fechaIngreso) ?? 1) / 30) + 1;

        echo $fechaIngreso. " : ". (Carbon::now()->diffInDays($fechaIngreso) ?? 1) . " = ". (intval((Carbon::now()->diffInDays($fechaIngreso) ?? 1) / 30) + 1) . "<br>";

        $result = new APC_InformeOt([
            "Sucursal" => $row[0],
            "FechaIngreso" => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[1]),
            "FechaCierre" => ($row[2] != "") ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[2]) : null,
            "Seccion" => $row[3],
            "TipoOt" => $row[4],
            "Folio" => $row[5],
            "Recepcionista" => $row[6],
            "Estado" => $row[7],
            "FechaEntrega" => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[8]),
            "FechaEntregaReal" => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[9]),
            "Marca" => $row[10],
            "Nombre" => $row[11],
            "Version" => $row[12],
            "Anio" => $row[13],
            "Patente" => $row[14],
            "VIN" => $row[15],
            "Dealer" => $row[16],
            "FechaFacturaVehiculo" => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[17]),
            "Color" => $row[18],
            "KilometrajeActual" => $row[19],
            "Cliente" => $row[20],
            "CompaniaSeguro" => $row[21],
            "NumeroSiniestro" => ($row[22] != "") ? $row[22] : null,
            "TotalServicios" => $row[23],
            "TotalRepuestos" => $row[24],
            "Neto" => $row[25],
            "PendienteFacturacion" => $row[26],
            "Grua8Anios" => $row[27],
            "ReingresoATaller" => $row[28],
            "ClientePrioritario" => $row[29],
            "PruebaDeRuta" => $row[30],
            "ComunicarACliente" => $row[31],
            "Campania" => $row[32],
            "ControlDeCalidad" => $row[33],
            "GeneraPresupuesto" => $row[34],
            "Atributo" => $row[35],
            "Horometro" => $row[36],
            "ObservacionOt" => $row[37],
            "SucursalID" => $h->getDato($row[0].$row[3], $idFlujo, 'sucursal', 0),
//            "EstadoInterno" => 1,
            "MarcaID" => MA_Marcas::where('Marca', $marca)->first()->ID ?? 0,
            "Tramo" => $tramo,

        ]);

        if($result){
            $this->contadorRegistro++;
        }

        return $result;

    }


    /**
     * @return string|array
     */
    public function uniqueBy()
    {
        return 'Folio';
    }

    public function batchSize(): int
    {
        return 1000;
    }

/*    public static function beforeImport(AfterImport $event)
    {

    }*/

    public function startRow(): int
    {
        return 2;
    }
}
