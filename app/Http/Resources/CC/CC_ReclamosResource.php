<?php

namespace App\Http\Resources\CC;

use Illuminate\Http\Resources\Json\JsonResource;

class CC_ReclamosResource extends JsonResource
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
            'id_reclamo' => $this->ID,
            'fecha_reclamo' => $this->FechaReclamo,
            'detalle_reclamo' => $this->DetalleReclamo,

        ];
    }
}
