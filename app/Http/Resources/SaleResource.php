<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
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
            'rut' => $this->client->Rut,
            'tipoCliente' => $this->client->TipoJuridicoID === 1 ? 'Natural' : ( $this->client->typeOfClient->TipoCliente === 2 ? 'Natural con giro' : 'Juridica' ),
            'nombre' => $this->client->Nombre,
            'direccion' => $this->client->Direccion,
            'comuna' => $this->client->commune->Comuna,
            'telefono' => $this->client->Telefono,
            'email' => $this->client->Email,
            'patente' => $this->Patente,
            'marca' => $this->brand->Marca,
            'modelo' => $this->carModel->Modelo,
            'fecha_venta' => $this->FechaVenta->format('d-m-Y') ,
            'vin' => $this->Vin,
        ];
    }
}
