<?php

namespace App\Imports;

use App\Models\APC_Stock;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Validators\Failure;

class ApcStockImport implements ToModel, WithHeadingRow,WithChunkReading, WithBatchInserts, SkipsOnFailure, WithEvents
{

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {


        return new APC_Stock([
            'Empresa' => $row['empresa'],
            'Sucursal' => $row['sucursal'],
            'Folio_Venta' => $row['folio_venta'],
            'Venta' => $row['venta'],
            'Estado_Venta' => $row['estado_venta'],
            'Fecha_Venta' => ($row['fecha_venta']!='') ? Carbon::createFromFormat("d-m-Y H:i:s",$row['fecha_venta'])->format('Y-m-d H:i:s') : null,
            'Tipo_Documento' => $row['tipo_documento_folio'],
            'Vendedor' => $row['vendedor'],
            'Fecha_Ingreso' => ($row['fecha_ingreso']!='') ? Carbon::createFromFormat("d-m-Y H:i:s",$row['fecha_ingreso'])->format('Y-m-d H:i:s') : null,
            'Fecha_Facturacion' => ($row['fecha_facturacion']!='') ? Carbon::createFromFormat("d-m-Y H:i:s",$row['fecha_facturacion'])->format('Y-m-d H:i:s') : null,
            'VIN' => $row['numero_vin'],
            'Marca' => $row['marca'],
            'Modelo' => $row['modelo'],
            'Version' => $row['version'],
            'Codigo_Version' => $row['codigo_version'],
            'Anio' => $row['ano'],
            'Kilometraje' => $row['kilometraje'],
            'Codigo_Interno' => $row['codigo_interno'],
            'Placa_Patente' => $row['placa_patente'],
            'Condicion_Vehículo' => $row['condicion_vehiculo'],
            'Color_Exterior' => $row['color_exterior'],
            'Color_Interior' => $row['color_interior'],
            'Precio_Venta_Total' => $row['precio_venta_total'],
            'Estado_AutoPro' => $row['estado_autopro'],
            'Dias_Stock' => $row['dias_stock'],
            'Estado_Dealer' => $row['estado_dealer'],
            'Bodega' => $row['bodega'],
            'Equipamiento' => $row['equipamiento'],
            'Numero_Motor' => $row['numero_motor'],
            'Numero_Chasis' => $row['numero_chasis'],
            'Proveedor' => $row['proveedor'],
            'Fecha_Disponibilidad' => ($row['fecha_disponibilidad']!='') ? Carbon::createFromFormat("d-m-Y H:i:s",$row['fecha_disponibilidad'])->format('Y-m-d H:i:s') : null,
            'Factura_Compra' => $row['factura_compra'] ?? 0,
            'Vencimiento_Documento' => ($row['vencimiento_documento']!='') ? Carbon::createFromFormat("d-m-Y H:i:s",$row['vencimiento_documento'])->format('Y-m-d H:i:s') : null,
            'Fecha_Compra' => ($row['fecha_compra']!='') ? Carbon::createFromFormat("d-m-Y H:i:s",$row['fecha_compra'])->format('Y-m-d H:i:s') : null,
            'Fecha_Vencto_Rev_tec' => ($row['fecha_vencto_revision_tecnica']!='') ? Carbon::createFromFormat("d-m-Y H:i:s",$row['fecha_vencto_revision_tecnica'])->format('Y-m-d H:i:s') : null,
            'N_Propietarios' => $row['n_propietarios'],
            'Folio_Retoma' => $row['folio_retoma'],
            'Fecha_Retoma' => ($row['fecha_retoma']!='') ? Carbon::createFromFormat("d-m-Y H:i:s",$row['fecha_retoma'])->format('Y-m-d H:i:s') : null,
            'Dias_Reservado' => $row['dias_reservado'],
            'Precio_Compra_Neto' => $row['precio_compra_neto'],
            'Gasto' => $row['gasto'],
            'Accesorios' => $row['accesorios'],
            'Total_Costo' => $row['total_costo'],
            'Precio_Lista' => $row['precio_lista'],
            'Margen' => $row['margen'],
//            'Margen_porcentaje' => $row[46],
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function onFailure(Failure ...$failures)
    {
        // Handle the failures how you'd like.

        Log::notice(print_r($failures, true));

        return true;

    }

    public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            BeforeImport::class => function(BeforeImport $event) {
                $creator = $event->reader->getProperties()->getCreator();
            },


            // Using a class with an __invoke method.
            BeforeSheet::class => new BeforeSheetHandler(),

            // Array callable, refering to a static method.
            AfterSheet::class => [self::class, 'afterSheet'],

        ];
    }


}
