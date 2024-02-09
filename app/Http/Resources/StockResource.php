<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->ID,
            'marca' => $this->Marca,
            'modelo' => $this->Modelo,
            'version' => $this->Version,
            'anio' => $this->Anno,
            'color' => $this->ColorExterior,
            'vin' => $this->VIN,
            'estado' => $this->DisponibleENissan,
            'precio' => $this->PrecioVenta,
            /*'CIT'*/
            'sucursal' => $this->Sucursal,
        ];
    }
}
