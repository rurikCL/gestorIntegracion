<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiSolicitudResource extends JsonResource
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
            'ReferenciaID' => $this->ReferenciaID,
            'ProveedorID' => $this->ProveedorID,
            'ApiID' => $this->ApiID,
            'Prioridad' => $this->Prioridad,
            'Peticion' => $this->Peticion,
            'CodigoRespuesta' => $this->CodigoRespuesta,
            'Respuesta' => $this->Respuesta,
            'Exito' => $this->Exito,
            'FechaPeticion' => $this->FechaPeticion,
            'FechaResolucion' => $this->FechaResolucion
        ];
    }
}
