<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RequestToApiController extends Controller
{
    public function apileads()
    {
        $response = Http::get('https://run.mocky.io/v3/leadsync');

        $lead = new Lead();

        $lead->FechaCreacion = $response;
        $lead->OrigenID = $response;
        $lead->SubOrigenID = $response;
        $lead->ClienteID = $response;
        $lead->SucursalID = $response;
        $lead->VendedorID = $response;
        $lead->MarcaID = $response;
        $lead->ModeloID = $response;
        $lead->VersionID = $response;
        $lead->EstadoID = $response;
        $lead->SubEstadoID = $response;
        $lead->Financiamiento = $response;
        $lead->CampanaID = $response;
        $lead->IntegracionID = $response;
        $lead->IDExterno = $response;
        $lead->ConcatID = $response;
        $lead->Asignado = $response;
        $lead->Llamado = $response;
        $lead->Agendado = $response;
        $lead->Venta = $response;
        $lead->ReferenciaID = $response;
        $lead->Cotizado = $response;
        $lead->Vendido = $response;
        $lead->FechaReAsignado = $response;
        $lead->Comentario = $response;
        $lead->Contesta = $response;

        $lead->save();

    }
}
