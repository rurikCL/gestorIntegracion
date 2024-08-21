<?php

namespace App\Imports;

use App\Models\APC_Repuestos;
use App\Models\APC_Sku;
use App\Models\APC_Stock;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ApcRepuestosImport implements ToModel, WithHeadingRow, WithBatchInserts
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        echo $row['fecha_de_creacion_ot'] . " ". $row['fecha_de_consumo_o_devolucion'] . " ". $row['fecha_liquidacion'] . " ". $row['fecha_facturacion'] . "<br>";

        return new APC_Repuestos([
            'Sucursal' => $row['sucursal'],
            'Recepcionista' => $row['recepcionista'],
            'Cliente' => $row['cliente'],
            'Fecha_Consumo' => Carbon::parse($row['fecha_de_consumo_o_devolucion'])->format('Y-m-d H:i:s') ?? null,
            'Folio_OT' => $row['folio_ot'] ?? null,
            'Grupo' => $row['grupo'],
            'Condicion' => $row['condicion'],
            'OT_Estado' => $row['ot_estado'],
            'OT_Seccion' => $row['ot_seccion'],
            'OT_Tipo' => $row['ot_tipo'],
            'Numero_Vin' => $row['numero_vin'],
            'Bodega' => $row['bodega'],
            'Ubicacion' => $row['ubicacion'],
            'Sub_Grupo' => $row['subgrupo'],
            'Clasificacion' => $row['clasificacion'],
            'Marca' => $row['marca'],
            'Version' => $row['version'],
            'Placa_Patente' => $row['placa_patente'],
            'Fecha_Creacion_OT' => Carbon::parse($row['fecha_de_creacion_ot'])->format('Y-m-d H:i:s') ?? null,
            'Tipo_Documento' => $row['tipo_documento'],
            'Folio_Documento' => $row['folio_documento'],
            'SKU' => $row['sku'],
            'Nombre' => $row['nombre'],
            'Cantidad' => $row['cantidad'] ?? null,
            'Costo' => $row['costo'] ?? null,
            'Sub_Total' => $row['sub_total'] ?? null,
            'Tipo_Cargo' => $row['tipo_cargo'] ?? null,
            'Fecha_Liquidacion' => Carbon::parse($row['fecha_liquidacion'])->format('Y-m-d H:i:s') ?? null,
            'Fecha_Facturacion' => Carbon::parse($row['fecha_facturacion'])->format('Y-m-d H:i:s') ?? null,
        ]);

    }

    public function batchSize(): int
    {
        return 1000;
    }
}
