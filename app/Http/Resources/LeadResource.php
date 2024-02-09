<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
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
            'fecha de creacion' => $this->FechaCreacion,
            'origen' => $this->OrigenID,
            'origen' => $this->origin,
            'sub-origen' => $this->SubOrigenID,
            'clienteID' => $this->ClienteID,
            'cliente' => $this->user,
            'SucursalID' => $this->branchOffice ,
            'VendedorID' => $this->VendedorID,
            'MarcaID' => $this->MarcaID,
            'ModeloID' => $this->ModeloID,
            'VersionID' => $this->VersionID,
            'EstadoID' => $this->EstadoID,
            'SubEstadoID' => $this->SubEstadoID,
            'Financiamiento' => $this->Financiamiento,
            'CampanaID' => $this->CampanaID,
            'IntegracionID' => $this->IntegracionID,
            'IDExterno' => $this->IDExterno,
            'ConcatID' => $this->ConcatID,
            'Asignado' => $this->Asignado,
            'Llamado' => $this->Llamado,
            'Agendado' => $this->Agendado,
            'Venta' => $this->Venta,
            'ReferenciaID' => $this->ReferenciaID,
            'Cotizado' => $this->Cotizado,
            'Vendido' => $this->Vendido,
            'FechaReAsignado' => $this->FechaReAsignado,
            'Comentario' => $this->Comentario,
            'Contesta' => $this->Contesta
        ];
    }
}
