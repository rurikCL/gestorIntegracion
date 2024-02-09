<?php

namespace App\Http\Resources\MA;

use Illuminate\Http\Resources\Json\JsonResource;

class MA_ClientesResource extends JsonResource
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
            'ID' => $this->ID,
            'ReferenciaID' => $this->ReferenciaID,
            'Nombre' => $this->Nombre,
            'Apellido' => $this->Apellido ?? '',
            'Rut' => $this->Rut,
            'Email' => $this->Email,
            'Telefono' => $this->Telefono,
            'Telefono2' => $this->Telefono2,
            'Telefono3' => $this->Telefono3,
            'FechaNacimiento' => $this->FechaNacimiento,
            'Direccion' => $this->Direccion
        ];
    }
}
