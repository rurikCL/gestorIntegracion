<?php

namespace App\Http\Resources\MA;

use Illuminate\Http\Resources\Json\JsonResource;

class MA_SucursalesResource extends JsonResource
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
            'sucursal' => $this->Sucursal,
            'direccion' => $this->Direccion,
            'marca_asociada' => $this->gerencia->marca->Marca ?? '',
            'id_intouch' => $this->H_IntouchID,
        ];
    }
}
