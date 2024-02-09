<?php

namespace App\Http\Resources\MA;

use Illuminate\Http\Resources\Json\JsonResource;

class MA_ModelosResource extends JsonResource
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
            'id_modelo' => $this->ID,
            'modelo' => $this->Modelo,
            'id_marca' => $this->MarcaID,
            'marca' => $this->marca->Marca,
            'id_intouch' => $this->H_IntouchID
        ];
    }
}
