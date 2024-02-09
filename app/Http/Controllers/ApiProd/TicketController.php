<?php

namespace App\Http\Controllers\ApiProd;

use App\Http\Controllers\Controller;
use App\Models\CC\CC_CallCenterAsignacionUsuario;
use App\Models\MA\MA_Sucursales;
use App\Models\TK\TKa_Tickets;
use App\Models\TK\TKc_Tickets;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {

    }

    public function store(Request $request)
    {
        $sucursal = MA_Sucursales::where('ID', $request->input('data.sucursalID'))
            ->first();

//        dd($sucursal);

        if ($sucursal) {
            if ($request->input('data.tipoTicket') == "ACC") {
                $ticket = new TKa_Tickets();
            } else if ($request->input('data.tipoTicket') == "VEN") {
                $ticket = new TKc_Tickets();
            } else if ($request->input('data.tipoTicket') == "OPE") {
                $ticket = new TKc_Tickets();
            } else {
                $ticket = new TKc_Tickets();
            }

            if ($request->input('data.categoria') == 13 && $request->input('data.subCategoria') == 42) {
                $asignacion = CC_CallCenterAsignacionUsuario::where('SucursalID', $sucursal->ID)
                    ->where('Activo', 1)
                    ->where('Tipo', 3)
                    ->first();
            } elseif ($request->input('data.categoria') == 13 && $request->input('data.subCategoria') == 44) {
                $asignacion = CC_CallCenterAsignacionUsuario::where('SucursalID', $sucursal->ID)
                    ->where('Activo', 1)
                    ->where('Tipo', 2)
                    ->first();
            } elseif ($request->input('data.categoria') == 14 && $request->input('data.subCategoria') == 43) {
                $asignacion = CC_CallCenterAsignacionUsuario::where('SucursalID', $sucursal->ID)
                    ->where('Activo', 1)
                    ->where('Tipo', 1)
                    ->first();
            }


            $ticket->FechaCreacion = date('Y-m-d H:i:s');
            $ticket->created_at = date('Y-m-d H:i:s');
            $ticket->EventoCreacionID = 145;
            $ticket->UsuarioCreacionID = $request->input('data.usuarioId');
            $ticket->title = $request->input('data.titulo');
            $ticket->detail = $request->input('data.detalle');
            $ticket->state = 1;
            $ticket->priority = $request->input('data.prioridad');
            $ticket->category = $request->input('data.categoria');
            $ticket->subCategory = $request->input('data.subCategoria');
            $ticket->management = $sucursal->GerenciaID;
            $ticket->zone = $sucursal->TipoSucursalID;
            $ticket->department = $sucursal->ID;
            $ticket->applicant = $request->input('data.usuarioId');
            $ticket->assigned = $asignacion->UsuarioID ?? 1;
            $ticket->save();

        } else {
            return response()->json(['status' => 0, 'messages' => 'No se ha encontrado la Sucursal'], 200);
        }
        return response()->json(['status' => 1,
            'messages' => 'Ticket ingresado correctamente',
            'ticketID' => $ticket->id], 200);
    }
}
