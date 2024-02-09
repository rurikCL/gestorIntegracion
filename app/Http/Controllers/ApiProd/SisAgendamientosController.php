<?php

namespace App\Http\Controllers\ApiProd;

use App\Http\Controllers\Controller;
use App\Models\SIS\SIS_Agendamientos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SisAgendamientosController extends Controller
{
    //
    public function store(Request $request)
    {
        $varUsuarioID = $request->input('data.usuarioID');
        $varClienteID = $request->input('data.clienteID');
        $varLlamadaID = $request->input('data.llamadaID');
        $varComentario = $request->input('data.comentario');
        $varSucursalID = $request->input('data.sucursalID');
        $varFechaAgenda = $request->input('data.fechaAgenda');
        $varMarcaAgenda = $request->input('data.marcaAgenda');
        $varModeloAgenda = $request->input('data.modeloAgenda');
        $varHoraAgenda = $request->input('data.horaAgenda');
        $varPatenteAgenda = $request->input('data.patenteAgenda');
        $varMantencionAgenda = $request->input('data.mantencionAgenda');
        $varAnno = $request->input('data.anno');
        $varVentaID = $request->input('data.ventaID');


        DB::transaction(function () use ($request) {

            $agendamiento = new SIS_Agendamientos();
            $agendamiento->FechaCreacion = date('Y-m-d H:i:s');
            $agendamiento->EventoCreacionID = 1;
            $agendamiento->UsuarioCreacionID = 1683; // Usuario por defecto de Integracion - Web Pompeyo
            $agendamiento->Nombre = $request->input('data.nombre');
            $agendamiento->SegundoNombre = $request->input('data.segundoNombre');
            $agendamiento->Apellido = $request->input('data.apellido');
            $agendamiento->SegundoApellido = $request->input('data.segundoApellido');
            $agendamiento->Rut = $request->input('data.rut');
            $agendamiento->Email = $request->input('data.email') ?? '';
            $agendamiento->Telefono = $request->input('data.telefono');
            $agendamiento->FechaNacimiento = $request->input('data.fechaNacimiento') ?? '';
            $agendamiento->Direccion = $request->input('data.direccion') ?? '';
            $agendamiento->save();
        });
        return response()->json(['messages' => 'Cliente creado correctamente'], 200);
    }
}
