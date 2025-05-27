<?php

namespace App\Http\Controllers\ApiProd;

use App\Http\Controllers\Controller;
use App\Models\MA\MA_Clientes;
use App\Models\VT\VT_Ventas;
use Illuminate\Http\Request;

class NegocioController extends Controller
{
    public function index()
    {

    }

    public function update(Request $request)
    {
        $idVenta = $request->input('data.idVenta');
        if ($idVenta != "") {
            $venta = VT_Ventas::where('ID', $idVenta)->first();

            // Datos de Cliente --------------------------------------
            if ($request->input('data.cliente')) {
                $cliente = MA_Clientes::where('ID', $venta->ClienteID)->first();
                $clienteLog = $cliente->clienteLog;

                if($request->input('data.cliente.nombre') != ""){
                    $updateData['Nombre'] = $request->input('data.cliente.nombre');
//                    $clienteLog->registrarLog($cliente->ID, $venta->UsuarioActualizacionID, 'Nombre', $cliente->Nombre, $request->input('data.cliente.nombre'), 'Actualización de nombre');
                }
                if($request->input('data.cliente.apellidoPaterno') != ""){
                    $updateData['ApellidoPaterno'] = $request->input('data.cliente.apellidoPaterno');
//                    $clienteLog->registrarLog($cliente->ID, $venta->UsuarioActualizacionID, 'ApellidoPaterno', $cliente->ApellidoPaterno, $request->input('data.cliente.apellidoPaterno'), 'Actualización de apellido paterno');
                }
                if($request->input('data.cliente.apellidoMaterno') != ""){
                    $updateData['ApellidoMaterno'] = $request->input('data.cliente.apellidoMaterno');
//                    $clienteLog->registrarLog($cliente->ID, $venta->UsuarioActualizacionID, 'ApellidoMaterno', $cliente->ApellidoMaterno, $request->input('data.cliente.apellidoMaterno'), 'Actualización de apellido materno');
                }
                if($request->input('data.cliente.rut') != ""){
                    $updateData['Rut'] = $request->input('data.cliente.rut');
//                    $clienteLog->registrarLog($cliente->ID, $venta->UsuarioActualizacionID, 'Rut', $cliente->Rut, $request->input('data.cliente.rut'), 'Actualización de rut');
                }
                if($request->input('data.cliente.email') != ""){
                    $updateData['Email'] = $request->input('data.cliente.email');
//                    $clienteLog->registrarLog($cliente->ID, $venta->UsuarioActualizacionID, 'Email', $cliente->Email, $request->input('data.cliente.email'), 'Actualización de email');
                }
                if($request->input('data.cliente.telefono') != ""){
                    $updateData['Telefono'] = $request->input('data.cliente.telefono');
//                    $clienteLog->registrarLog($cliente->ID, $venta->UsuarioActualizacionID, 'Telefono', $cliente->Telefono, $request->input('data.cliente.telefono'), 'Actualización de teléfono');
                }
                $cliente->update($updateData);

            }

            // Datos de Venta --------------------------------------
            if(request('data.venta')){
                if($request->input('data.venta.tipoVenta') != ""){
                    $venta->TipoVentaID = $request->input('data.venta.tipoVenta');
                }
                if($request->input('data.venta.tipoCredito') != ""){
                    $venta->TipoCreditoID = $request->input('data.venta.tipoCredito');
                }
                if($request->input('data.venta.entidadFinanciera') != ""){
                    $venta->EntidadFinancieraID = $request->input('data.venta.entidadFinanciera');
                }
                if($request->input('data.venta.subEntidadFinanciera') != ""){
                    $venta->EntidadFinancieraID = $request->input('data.venta.subEntidadFinanciera');
                }

                // cuotas, tasaInteres, valorCuota, pie, saldo, codigoOp
                if($request->input('data.venta.cuotas') != ""){
                    $venta->Cuotas = $request->input('data.venta.cuotas');
                }
                if($request->input('data.venta.tasaInteres') != ""){
                    $venta->TasaInteres = $request->input('data.venta.tasaInteres');
                }
                if($request->input('data.venta.valorCuota') != ""){
                    $venta->ValorCuota = $request->input('data.venta.valorCuota');
                }
                if($request->input('data.venta.pie') != ""){
                    $venta->Pie = $request->input('data.venta.pie');
                }
                if($request->input('data.venta.saldo') != ""){
                    $venta->Saldo = $request->input('data.venta.saldo');
                }
                if($request->input('data.venta.codigoOp') != ""){
                    $venta->CodigoOperacion = $request->input('data.venta.codigoOp');
                }

                $venta->save();

            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'ID de venta no proporcionado'
            ], 400);
        }


    }

    public function destroy($id)
    {
    }
}
