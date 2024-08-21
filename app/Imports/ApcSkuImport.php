<?php

namespace App\Imports;

use App\Models\APC_Sku;
use App\Models\APC_Stock;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ApcSkuImport implements ToModel, WithHeadingRow, WithBatchInserts
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        return new APC_Sku([
            'Sucursal' => $row['sucursal'],
            'Bodega' => $row['bodega'] ?? null,
            'Ubicacion' => $row['ubicacion'] ?? null,
            'Cod_Sku' => $row['codigo_sku'],
            'Sku' => $row['sku'],
            'Saldo' => $row['saldo'],
            'Cup' => $row['cup'],
            'Total' => $row['costo_total'],
            'Grupo' => $row['grupo'],
            'Sub_Grupo' => $row['subgrupo'],
            'Marca' => $row['marca'],
            'Clasificacion' => $row['clasificacion'],
            'Condicion' => $row['condicion'],
            'Categoria' => $row['categoria'],
            'Fecha_Primera_Compra' => Carbon::parse($row['primera_compra'])->format('Y-m-d H:i:s'),
            'Fecha_Ultima_Compra' => Carbon::parse($row['ultima_compra'])->format('Y-m-d H:i:s'),
            'Fecha_Ultima_Venta' => Carbon::parse($row['fecha_ultima_venta'])->format('Y-m-d H:i:s'),
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }
}
