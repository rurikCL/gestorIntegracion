<?php

namespace App\Http\Resources\MA;

use Illuminate\Http\Resources\Json\JsonResource;

class MA_GerenciasResource extends JsonResource
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
            'id' => $this->MarcaAsociada,
            'marca' => $this->Gerencia,
            'id_intouch' => $this->marca->H_IntouchID ?? 0
        ];
    }
}
