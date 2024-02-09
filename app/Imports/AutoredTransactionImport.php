<?php

namespace App\Imports;

use App\Models\FLU\FLU_Cargas;
use App\Models\FLU\FLU_Homologacion;
use App\Models\MA\MA_Usuarios;
use App\Models\SIS\SIS_AutoRedTransaccion;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Validators\Failure;

class AutoredTransactionImport implements toModel, WithUpserts, WithEvents, SkipsOnFailure
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    private $carga = null;
    private $flujoID = 0;
    private $contadorRegistro = 0;
    private $contErrores = 0;

    use Importable, RegistersEventListeners, SkipsFailures;

    public function __construct($carga)
    {
        $this->carga = $carga;
    }

    public function setFlujoID($flujoID)
    {
        $this->flujoID = $flujoID;
    }

    public static function beforeImport(BeforeImport $event)
    {
        $totalRows = 0;
        Log::info("Inicio de importacion de Transactions Autored");
        $totalRows = $event->getReader()->getTotalRows();

        if (!empty($totalRows)) {
            $totalRows = $totalRows['Worksheet'] - 1;
        }
//        FLU_Cargas::where('ID', $event->getConcernable()->carga->ID)
            $event->getConcernable()->carga
                ->update(['Registros' => $totalRows,
                'RegistrosCargados' => 0,
                'RegistrosFallidos' => 0
            ]);
    }

    public static function afterImport(AfterImport $event)
    {
        Log::info("Termino de importacion de Transactions Autored");
        $event->getConcernable()->carga
            ->update(['RegistrosCargados' => $event->getConcernable()->contadorRegistro,
                'RegistrosFallidos' => $event->getConcernable()->contErrores,
                'Estado' => 2
            ]);
    }

    public function model(array $row)
    {
        // excluye el header
        if ($row[41] == "ID") return null;

        ++$this->contadorRegistro;

        try {
            $fechaCreacion = Carbon::createFromFormat("d-m-Y H:i", $row[0])->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            $fechaCreacion = Carbon::now()->format("Y-m-d H:i:s");
        }

        try {
            $fechaRecepcion = Carbon::createFromFormat("d-m-Y H:i", $row[38])->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            $fechaRecepcion = null;
        }

        try {
            $fechaInspeccion = Carbon::createFromFormat("d-m-Y H:i", $row[40])->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            $fechaInspeccion = null;
        }

        $sucursal = $row[9];
        $emailVendedor = $row[11];
        $sucursalID = FLU_Homologacion::getDato($sucursal, $this->flujoID, 'sucursal', 1);

        $vendedorID = MA_Usuarios::where('Email', $emailVendedor)->first();
        $vendedorID = $vendedorID ? $vendedorID->ID : null;

        return new SIS_AutoRedTransaccion([
            //
            'ID' => 0,
            'FechaCreacion' => $fechaCreacion,
            'Patente' => $row[2],
            'Marca' => $row[3],
            'Modelo' => $row[4],
            'Ano' => $row[5],
            'Km' => $row[6],
            'Version' => $row[7],
            'Color' => $row[8],
            'Sucursal' => $sucursal,
            'Vendedor' => $row[10],
            'EmailVendedor' => $emailVendedor,
            'CodigoVendedor' => $row[12],
            'Creador' => $row[13],
            'Estado' => $row[14],
            'MotivoRechazo' => $row[15],
            'DetalleRechazo' => $row[16],
            'PrecioOferta' => $row[17],
            'AutorPrecio' => $row[18],
            'AtendidaPor' => $row[19],
            'PrecioSugerido' => $row[20],
            'PrecioPublicacion' => $row[21],
            'PrecioVenta' => $row[22],
            'NombreCliente' => $row[23],
            'RutCliente' => $row[24],
            'EmailCliente' => $row[25],
            'TelefonoCliente' => $row[26],
            'CelularCliente' => $row[27],
            'TelefonoOficinaCliente' => $row[28],
            'MarcaCliente' => $row[29],
            'ModeloCliente' => $row[30],
            'FinanciamientoCliente' => $row[31],
            'ComentarioCliente' => $row[32],
            'IDtransaccion' => $row[33],
            'Origen' => $row[34],
            'TipoCompra' => $row[35],
            'Procedencia' => $row[36],
            'VehiculoRecibido' => $row[37],
            'FechaRecepcion' => $fechaRecepcion,
            'Inspeccion' => $row[39],
            'FechaInspeccion' => $fechaInspeccion,
            'IDAutoRed' => $row[41],

            'SucursalID' => $sucursalID,
            'VendedorID' => $vendedorID
        ]);
    }

    public function uniqueBy()
    {
        return 'IDtransaccion';
    }


    public function onFailure(Failure ...$failures)
    {
        // Handle the failures how you'd like.
    }

/*    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }*/
}
