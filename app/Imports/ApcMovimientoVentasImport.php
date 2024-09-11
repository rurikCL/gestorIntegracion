<?php

namespace App\Imports;

use App\Models\APC_MovimientoVentas;
use App\Models\APC_Repuestos;
use App\Models\APC_Sku;
use App\Models\APC_Stock;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ApcMovimientoVentasImport implements ToModel, WithHeadingRow, WithBatchInserts
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        return new APC_MovimientoVentas([
            'Detalle' => $row["venta_detalle"],
            'Sucursal' => $row["sucursal"],
            'TipoDocumento' => $row["tipo_documento"],
            'Folio' => $row["folio"],
            'FechaDocumento' => Carbon::parse($row["fecha_documento"])->format('Y-m-d H:i:s') ?? null,
            'Estado' => $row["estado"],
            'Vendedor' => $row["vendedor"],
            'Cliente' => $row["cliente"],
            'Sku' => $row["sku"],
            'Nombre' => $row["nombre"],
            'Grupo' => $row["grupo"],
            'Bodega' => $row["bodega"],
            'Ubicacion' => $row["ubicacion"],
            'UnidadMedida' => $row["unidad_medida"],
            'Cantidad' => $row["cantidad"],
            'PrecioUnitario' => $row["precio_unitario"],
            'Valor' => $row["valor"],
            'SubTotal' => $row["subtotal"],
            'TipoTransaccion' => $row["tipo_transaccion"],
            'SubGrupo' => $row["subgrupo"],
            'Venta' => $row["venta"],
            'UsuarioCreacion' => $row["usuario_creacion"],
            'FechaCreacion' => Carbon::parse($row["fecha_creacion"])->format('Y-m-d H:i:s') ?? null,
            'FolioOt' => $row["folio_ot"],
            'DescripcionOt' => $row["descripcion_ot"],
            'TipoCargo' => $row["tipo_cargo"],
            'FechaEmision' => Carbon::parse($row["fecha_emision"])->format('Y-m-d H:i:s') ?? null,
            'RutCliente' => $row["rut_cliente"],
            'SeccionOt' => $row["ot_seccion"],
            'NroNotaVenta' => $row["n_nota_venta"],
            'FolioFactura' => $row["folio_factura"],
            'FechaFacturacion' => Carbon::parse($row["fecha_facturacion"])->format('Y-m-d H:i:s') ?? null,
        ]);

    }

    public function batchSize(): int
    {
        return 1000;
    }
}
