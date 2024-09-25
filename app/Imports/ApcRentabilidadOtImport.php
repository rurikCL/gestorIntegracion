<?php

namespace App\Imports;

use App\Models\APC_MovimientoVentas;
use App\Models\APC_RentabilidadOt;
use App\Models\APC_Repuestos;
use App\Models\APC_Sku;
use App\Models\APC_Stock;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\AfterImport;

class ApcRentabilidadOtImport implements ToModel, WithBatchInserts, WithEvents, WithStartRow
{

    use RegistersEventListeners;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    private $carga = null;
    private $contadorRegistro = 0;
    private $contErrores = 0;
    private $errores = [];

    public function __construct($carga)
    {
        $this->carga = $carga;
    }
    public function model(array $row)
    {
        $this->contadorRegistro = $this->carga->RegistrosCargados ?? 0;
        $contErrores = $this->carga->RegistrosFallidos ?? 0;
        $idCarga = $this->carga->ID;

        return new APC_RentabilidadOt([
            'Sucursal' => $row[0],
            'FechaFacturacion' => $row[1],
            'TipoDocumento' => $row[2],
            'TipoTrabajoOT' => $row[3],
            'Folio' => $row[4],
            'FolioOT' => $row[5],
            'FechaOT' => $row[6],
            'OTTipo' => $row[7],
            'OTSeccion' => $row[8],
            'ClienteOT' => $row[9],
            'ClienteRut' => $row[10],
            'ClienteDireccion' => $row[11],
            'ClienteComuna' => $row[12],
            'ClienteCiudad' => $row[13],
            'ClienteTelefonos' => $row[14],
            'ClienteEmail' => $row[15],
            'TipoCargoServicio' => $row[16],
            'VentaMO' => $row[17],
            'CostoMO' => $row[18],
            'MargenMO' => $row[19],
            'MargenMOPorcentaje' => $row[20],
            'TotalInsumos' => $row[21],
            'TotalSeguro' => $row[22],
            'VentaCarroceria' => $row[23],
            'CostoCarroceria' => $row[24],
            'MargenCarroceria' => $row[25],
            'MargenCarroceriaPorcentaje' => $row[26],
            'VentaServicioTerceros' => $row[27],
            'CostoServicioTerceros' => $row[28],
            'MargenServicioTerceros' => $row[29],
            'MargenTercerosPorcentaje' => $row[30],
            'VentaRepuestos' => $row[31],
            'CostoRepuestos' => $row[32],
            'MargenRepuestos' => $row[33],
            'MargenRepuestosPorcentaje' => $row[34],
            'TotalMaterialML' => $row[35],
            'CostoMaterialML' => $row[36],
            'MargenMaterialML' => $row[37],
            'MargenMaterialPje' => $row[38],
            'VentaLubricantes' => $row[39],
            'CostoLubricantes' => $row[40],
            'MargenLubricantes' => $row[41],
            'MargenLubricantesPorcentaje' => $row[42],
            'TotalDeducible' => $row[43],
            'TotalVenta' => $row[44],
            'TotalCosto' => $row[45],
            'TotalMargen' => $row[46],
            'TotalMargenPorcentaje' => $row[47],
            'TotalNetoFacturado' => $row[48],
            'Descuestos' => $row[49],
            'ClienteNombre2' => $row[50],
            'ClienteRut2' => $row[51],
            'ClienteDireccion2' => $row[52],
            'ClienteComuna2' => $row[53],
            'ClienteCiudad2' => $row[54],
            'ClienteTelefonos2' => $row[55],
            'ClienteEmail2' => $row[56],
            'Marca' => $row[57],
            'Modelo' => $row[58],
            'NumeroVIN' => $row[59],
            'Chasis' => $row[60],
            'Patente' => $row[61],
            'Kilometraje' => $row[62],
            'Mecanico' => $row[63],
            'Recepcionista' => $row[64],
            'FolioGarantia' => $row[65],
            'TipoMantención' => $row[66],
        ]);

    }

    public function batchSize(): int
    {
        return 1000;
    }

    public static function afterImport(AfterImport $event){

        dd($event);

    }

    public function startRow(): int
    {
        return 2;
    }
}
