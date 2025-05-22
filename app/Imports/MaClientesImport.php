<?php

namespace App\Imports;

use App\Models\APC_InformeOt;
use App\Models\APC_MovimientoVentas;
use App\Models\APC_RentabilidadOt;
use App\Models\APC_Repuestos;
use App\Models\APC_Sku;
use App\Models\APC_Stock;
use App\Models\FLU\FLU_Homologacion;
use App\Models\MA\MA_Clientes;
use App\Models\MA\MA_Marcas;
use App\Models\MA\MA_Sucursales;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Events\AfterImport;

class MaClientesImport implements ToModel, WithBatchInserts, WithEvents, WithStartRow, WithUpserts
{

    use RegistersEventListeners;
    use Importable, SkipsFailures;
    use RemembersRowNumber;

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

        $result = new MA_Clientes([
            'ID' => $row[0],
            "Nombre" => $row[2],
            "SegundoNombre" => $row[3],
            "Apellido" => $row[4],
            "SegundoApellido" => $row[5],
        ]);
        $this->contadorRegistro++;

        return $result;
    }


    /**
     * @return string|array
     */
    public function uniqueBy()
    {
        return 'ID';
    }

    public function batchSize(): int
    {
        return 10000;
    }

    public function afterImport(AfterImport $event)
    {

        if ($this->carga) {
            $this->carga->RegistrosCargados = $this->rowNumber;
            $this->carga->RegistrosFallidos = $this->contErrores;
            $this->carga->Estado = 'Procesado';
            $this->carga->save();
        }
    }

    public function startRow(): int
    {
        return 2;
    }

    public function getRegistrosCargados()
    {
        return $this->contadorRegistro;
    }

    public function getRegistrosFallidos()
    {
        return $this->contErrores;
    }
}
