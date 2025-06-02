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
        $respuesta = [];

        if ($idVenta != "") {
            $venta = VT_Ventas::where('ID', $idVenta)->first();

            try{
                // Datos de Cliente --------------------------------------
                if ($request->input('data.cliente')) {
                    $cliente = MA_Clientes::where('ID', $venta->ClienteID)->first();
//                $clienteLog = $cliente->clienteLog;

                    if($request->input('data.cliente.nombre') != ""){
                        $updateData['Nombre'] = $request->input('data.cliente.nombre');
                        $respuesta['Nombre']= [
                            'old_data' => $cliente->Nombre,
                            'new_data' => $updateData['Nombre']
                        ];
//                    $clienteLog->registrarLog($cliente->ID, $venta->UsuarioActualizacionID, 'Nombre', $cliente->Nombre, $request->input('data.cliente.nombre'), 'Actualización de nombre');
                    }
                    if($request->input('data.cliente.segundoNombre') != ""){
                        $updateData['SegundoNombre'] = $request->input('data.cliente.segundoNombre');
                        $respuesta['SegundoNombre']= [
                            'old_data' => $cliente->SegundoNombre,
                            'new_data' => $updateData['SegundoNombre']
                        ];
//                    $clienteLog->registrarLog($cliente->ID, $venta->UsuarioActualizacionID, 'Nombre', $cliente->Nombre, $request->input('data.cliente.nombre'), 'Actualización de nombre');
                    }
                    if($request->input('data.cliente.apellidoPaterno') != ""){
                        $updateData['Apellido'] = $request->input('data.cliente.apellidoPaterno');
                        $respuesta['ApellidoPaterno']= [
                            'old_data' => $cliente->Apellido,
                            'new_data' => $updateData['Apellido']
                        ];
//                    $clienteLog->registrarLog($cliente->ID, $venta->UsuarioActualizacionID, 'ApellidoPaterno', $cliente->ApellidoPaterno, $request->input('data.cliente.apellidoPaterno'), 'Actualización de apellido paterno');
                    }
                    if($request->input('data.cliente.apellidoMaterno') != ""){
                        $updateData['SegundoApellido'] = $request->input('data.cliente.apellidoMaterno');
                        $respuesta['ApellidoMaterno']= [
                            'old_data' => $cliente->SegundoApellido,
                            'new_data' => $updateData['SegundoApellido']
                        ];
//                    $clienteLog->registrarLog($cliente->ID, $venta->UsuarioActualizacionID, 'ApellidoMaterno', $cliente->ApellidoMaterno, $request->input('data.cliente.apellidoMaterno'), 'Actualización de apellido materno');
                    }
                    if($request->input('data.cliente.rut') != ""){
                        $updateData['Rut'] = $request->input('data.cliente.rut');
                        $respuesta['Rut']= [
                            'old_data' => $cliente->Rut,
                            'new_data' => $updateData['Rut']
                        ];
//                    $clienteLog->registrarLog($cliente->ID, $venta->UsuarioActualizacionID, 'Rut', $cliente->Rut, $request->input('data.cliente.rut'), 'Actualización de rut');
                    }
                    if($request->input('data.cliente.email') != ""){
                        $updateData['Email'] = $request->input('data.cliente.email');
                        $respuesta['Email']= [
                            'old_data' => $cliente->Email,
                            'new_data' => $updateData['Email']
                        ];
//                    $clienteLog->registrarLog($cliente->ID, $venta->UsuarioActualizacionID, 'Email', $cliente->Email, $request->input('data.cliente.email'), 'Actualización de email');
                    }
                    if($request->input('data.cliente.telefono') != ""){
                        $updateData['Telefono'] = $request->input('data.cliente.telefono');
                        $respuesta['Telefono']= [
                            'old_data' => $cliente->Telefono,
                            'new_data' => $updateData['Telefono']
                        ];
//                    $clienteLog->registrarLog($cliente->ID, $venta->UsuarioActualizacionID, 'Telefono', $cliente->Telefono, $request->input('data.cliente.telefono'), 'Actualización de teléfono');
                    }
                    $res = MA_Clientes::find($cliente->ID)->update($updateData);
                    $cliente->refresh();
//                dump($cliente);

                }

                // Datos de Venta --------------------------------------
                if(request('data.venta')){
                    if($request->input('data.venta.tipoVenta') != ""){
                        $respuesta['TipoVenta'] = [
                            'old_data' => $venta->TipoVentaID,
                            'new_data' => $request->input('data.venta.tipoVenta')
                        ];
                        $venta->TipoVentaID = $request->input('data.venta.tipoVenta');

                    }
                    if($request->input('data.venta.tipoCredito') != ""){
                        $respuesta['TipoCredito'] = [
                            'old_data' => $venta->TipoCredito,
                            'new_data' => $request->input('data.venta.tipoCredito')
                        ];
                        $venta->TipoCredito = $request->input('data.venta.tipoCredito');
                    }
                    if($request->input('data.venta.entidadFinanciera') != ""){
                        $respuesta['EntidadFinanciera'] = [
                            'old_data' => $venta->EntidadFinancieraID,
                            'new_data' => $request->input('data.venta.entidadFinanciera')
                        ];
                        $venta->EntidadFinancieraID = $request->input('data.venta.entidadFinanciera');
                    }
                    if($request->input('data.venta.subEntidadFinanciera') != ""){
                        $respuesta['SubEntidadFinanciera'] = [
                            'old_data' => $venta->EntidadFinancieraID,
                            'new_data' => $request->input('data.venta.subEntidadFinanciera')
                        ];
                        $venta->EntidadFinancieraID = $request->input('data.venta.subEntidadFinanciera');
                    }

                    // cuotas, tasaInteres, valorCuota, pie, saldo, codigoOp
                    if($request->input('data.venta.cuotas') != ""){
                        $respuesta['Cuotas'] = [
                            'old_data' => $venta->CantidadCuota,
                            'new_data' => $request->input('data.venta.cuotas')
                        ];
                        $venta->CantidadCuota = $request->input('data.venta.cuotas');
                    }
                    if($request->input('data.venta.tasaInteres') != ""){
                        $respuesta['TasaInteres'] = [
                            'old_data' => $venta->TasaInteres,
                            'new_data' => $request->input('data.venta.tasaInteres')
                        ];
                        $venta->TasaInteres = $request->input('data.venta.tasaInteres');
                    }
                    if($request->input('data.venta.valorCuota') != ""){
                        $respuesta['ValorCuota'] = [
                            'old_data' => $venta->ValorCuota,
                            'new_data' => $request->input('data.venta.valorCuota')
                        ];
                        $venta->ValorCuota = $request->input('data.venta.valorCuota');
                    }
                    if($request->input('data.venta.pie') != ""){
                        $respuesta['Pie'] = [
                            'old_data' => $venta->Pie,
                            'new_data' => $request->input('data.venta.pie')
                        ];
                        $venta->Pie = $request->input('data.venta.pie');
                    }
                    if($request->input('data.venta.saldo') != ""){
                        $respuesta['Saldo'] = [
                            'old_data' => $venta->SaldoFinanciar,
                            'new_data' => $request->input('data.venta.saldo')
                        ];
                        $venta->SaldoFinanciar = $request->input('data.venta.saldo');
                    }
                    if($request->input('data.venta.codigoOp') != ""){
                        $respuesta['CodigoOperacion'] = [
                            'old_data' => $venta->NumeroContrato,
                            'new_data' => $request->input('data.venta.codigoOp')
                        ];
                        $venta->NumeroContrato = $request->input('data.venta.codigoOp');
                    }

                    $venta->save();


                }
            }
            catch (\Exception $exception){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al actualizar la venta: ' . $exception->getMessage()
                ], 500);
            }

        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'ID de venta no proporcionado'
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Venta actualizada correctamente',
            'response' => $respuesta
        ]);
    }

    public function destroy($id)
    {
    }
}
