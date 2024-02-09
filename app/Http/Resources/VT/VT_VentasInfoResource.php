<?php

namespace App\Http\Resources\VT;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class VT_VentasInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $fechaFactura = ($this->FechaFactura) ? Carbon::parse($this->FechaFactura)->format("Y-m-d") : "0000-00-00 00:00:00" ;
        $fechaUltimaCuota = ($this->FechaFactura) ? Carbon::parse($this->FechaFactura)->addMonths($this->CantidadCuota)->format("Y-m-d") : "0000-00-00 00:00:00" ;

        return [
            'VentaID' => $this->ID,
            'FechaFactura' => $fechaFactura,
            'FechaUltimaCuota' => $fechaUltimaCuota,
            'Patente' => $this->Patente,
            'Vin' => $this->Vin,
            'GerenciaID' => $this->Sucursal->GerenciaID,
            'Sucursal' => $this->Sucursal->Sucursal,
            'Modelo' => $this->Modelo->Modelo,
            'TipoCredito'=> $this->CotizacionesTipoCredito->TipoCredito ?? null,
            'TipoMantencion' => $this->TipoMantencion->Tipo ?? null,
            'IdMantencion' => $this->Optiman->ID ?? null,
//            'Mantencion10_id' => $this->Optiman->mantencion10_id ?? null,
//            'Mantencion15_id' => $this->Optiman->mantencion15_id ?? null,
//            'Mantencion20_id' => $this->Optiman->mantencion20_id ?? null,
//            'Mantencion30_id' => $this->Optiman->mantencion30_id ?? null,
//            'Mantencion40_id' => $this->Optiman->mantencion40_id ?? null,
//            'Mantencion45_id' => $this->Optiman->mantencion45_id ?? null,
            'Mantencion10_fecha' => $this->Optiman->mantencion10_fecha ?? 'No realizada',
            'Mantencion15_fecha' => $this->Optiman->mantencion15_fecha ?? 'No realizada',
            'Mantencion20_fecha' => $this->Optiman->mantencion20_fecha ?? 'No realizada',
            'Mantencion30_fecha' => $this->Optiman->mantencion30_fecha ?? 'No realizada',
            'Mantencion40_fecha' => $this->Optiman->mantencion40_fecha ?? 'No realizada',
            'Mantencion45_fecha' => $this->Optiman->mantencion45_fecha ?? 'No realizada',
            ];
    }
}
