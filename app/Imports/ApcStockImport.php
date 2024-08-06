<?php

namespace App\Imports;

use App\Models\APC_Stock;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;

class ApcStockImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new APC_Stock([
            'Empresa' => $row[0],
            'Sucursal' => $row[1],
            'Folio_Venta' => $row[2],
            'Venta' => $row[3],
            'Estado_Venta' => $row[4],
            'Fecha_Venta' => ($row[5]) ? Carbon::createFromFormat("d-m-Y H:i:s",$row[5])->format('Y-m-d H:i:s') : null,
            'Tipo_Documento' => $row[6],
            'Vendedor' => $row[7],
            'Fecha_Ingreso' => ($row[8]) ? Carbon::createFromFormat("d-m-Y H:i:s",$row[8])->format('Y-m-d H:i:s') : null,
            'Fecha_Facturacion' => ($row[9]) ? Carbon::createFromFormat("d-m-Y H:i:s",$row[9])->format('Y-m-d H:i:s') : null,
            'VIN' => $row[10],
            'Marca' => $row[11],
            'Modelo' => $row[12],
            'Version' => $row[13],
            'Codigo_Version' => $row[14],
            'Anio' => $row[15],
            'Kilometraje' => $row[16],
            'Codigo_Interno' => $row[17],
            'Placa_Patente' => $row[18],
            'Condicion_Vehículo' => $row[19],
            'Color_Exterior' => $row[20],
            'Color_Interior' => $row[21],
            'Precio_Venta_Total' => $row[22],
            'Estado_AutoPro' => $row[23],
            'Dias_Stock' => $row[24],
            'Estado_Dealer' => $row[25],
            'Bodega' => $row[26],
            'Equipamiento' => $row[27],
            'Numero_Motor' => $row[28],
            'Numero_Chasis' => $row[29],
            'Proveedor' => $row[30],
            'Fecha_Disponibilidad' => $row[31],
            'Factura_Compra' => $row[32],
            'Vencimiento_Documento' => $row[33],
            'Fecha_Compra' => $row[34],
            'Fecha_Vencto_Rev_tec' => $row[35],
            'N_Propietarios' => $row[36],
            'Folio_Retoma' => $row[37],
            'Fecha_Retoma' => $row[38],
            'Dias_Reservado' => $row[39],
            'Precio_Compra_Neto' => $row[40],
            'Gasto' => $row[41],
            'Accesorios' => $row[42],
            'Total_Costo' => $row[43],
            'Precio_Lista' => $row[44],
            'Margen' => $row[45],
            'Margen_porcentaje' => $row[46],
        ]);
    }
}
