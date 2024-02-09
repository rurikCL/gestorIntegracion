<?php

namespace App\Http\Resources\MA;

use Illuminate\Http\Resources\Json\JsonResource;

class MA_MarcasResource extends JsonResource
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
            'id_intouch' => $this->H_IntouchID
        ];
    }
}
