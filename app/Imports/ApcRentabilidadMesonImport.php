<?php

namespace App\Imports;

use App\Models\APC_MovimientoVentas;
use App\Models\APC_RentabilidadMeson;
use App\Models\APC_Repuestos;
use App\Models\APC_Sku;
use App\Models\APC_Stock;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ApcRentabilidadMesonImport implements ToModel, WithHeadingRow, WithBatchInserts
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
//        dd($row);
        return new APC_RentabilidadMeson([
            "Sucursal" => $row["sucursal"],
            "FechaFacturacion" => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row["fecha_facturacion"]),
            "TipoDocumento" => $row["tipo_documento"],
            "Folio" => $row["folio"],
            "Vendedor" => $row["vendedor"],
            "Cliente" => $row["cliente"],
            "Rut" => $row["rut"],
            "Digito" => $row["digito"],
            "CodigoUnicoExtranjero" => $row["codigo_unico_extranjero"],
            "SKU" => $row["sku"],
            "NombreSKU" => $row["nombre_sku"],
            "Marca" => $row["marca"],
            "GrupoSKU" => $row["grupo_sku"],
            "SubGrupoSKU" => $row["subgrupo_sku"],
            "UnidadMediaSKU" => $row["unidad_medida_sku"],
            "Cantidad" => $row["cantidad"],
            "Venta" => $row["venta"],
            "Costo" => $row["costo"],
            "Margen" => $row["margen"],
            "PorcentajeMargen" => $row["margen"],
        ]);

    }

    public function batchSize(): int
    {
        return 1000;
    }


}
