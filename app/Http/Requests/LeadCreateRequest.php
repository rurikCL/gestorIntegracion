<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'FechaCreacion' => 'required',
            'OrigenID' => 'required',
            'SubOrigenID' => 'required',
            'ClienteID' => 'required',
            'SucursalID' => 'required',
            'VendedorID' => 'required',
            'MarcaID' => 'required',
            'ModeloID' => 'required',
            'VersionID' => 'required',
            'EstadoID' => 'required',
            'SubEstadoID' => 'required',
            'Financiamiento' => 'required',
            'CampanaID' => 'required',
            'IntegracionID' => 'required',
            'IDExterno' => 'required',
            'ConcatID' => 'required',
            'Asignado' => 'required',
            'Llamado' => 'required',
            'Agendado' => 'required',
            'Venta' => 'required',
            'ReferenciaID' => 'required',
            'Cotizado' => 'required',
            'Vendido' => 'required',
            'FechaReAsignado' => 'required',
            'Comentario' => 'required',
            'Contesta' => 'required',
        ];
    }
}
