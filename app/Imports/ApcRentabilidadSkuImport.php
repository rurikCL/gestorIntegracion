<?php

namespace App\Imports;

use App\Models\APC_RentabilidadSku;
use App\Models\APC_Sku;
use App\Models\APC_Stock;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ApcRentabilidadSkuImport implements ToModel, WithHeadingRow, WithBatchInserts
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

//        dd($row);
        return new APC_RentabilidadSku([
            'Sucursal' => $row['sucursal'],
            'TipoDocumento' => $row['tipo_documento'],
            'Folio' => $row['folio'],
            'FechaFacturacion' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_facturacion']),
            'FolioOt' => $row['folio_ot'],
            'Servicio' => $row['tipo_cargo_servicio'],
            'SKU' => $row['sku'],
            'Nombre' => $row['nombre'],
            'Grupo' => $row['grupo'],
            'SubGrupo' => $row['subgrupo'],
            'Marca' => $row['marca'],
            'Medida' => $row['unidad_medida'],
            'Cantidad' => $row['cantidad'],
            'Mecanico' => $row['mecanico'],
            'Venta' => $row['venta'],
            'Costo' => $row['costo'],
            'Margen' => $row['margen'],
            'Porcentaje' => 0,
            'Recepcionista' => $row['recepcionista'],
        ]);
    }
    public function batchSize(): int
    {
        return 1000;
    }
}
