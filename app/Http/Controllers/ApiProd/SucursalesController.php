<?php

namespace App\Http\Controllers\ApiProd;

use App\Http\Controllers\Controller;
use App\Http\Resources\MA\MA_SucursalesCollection;
use App\Models\MA\MA_Sucursales;
use Illuminate\Http\Request;

class SucursalesController extends Controller
{
    public function index(Request $request)
    {

        if ($request->input('data.tipoSucursal')) {
            $tipoSucursal = $request->input('data.tipoSucursal');

            if ($tipoSucursal == 2) {

                if (!$request->input('data.entrada')) {
                    $sucursales = MA_Sucursales::with('gerencia')
                        ->where('Activa', 1)
                        ->where('VisibleCC', 1)
                        ->where('Sucursal', 'like', '%Servicio%')
                        ->orderBy('Sucursal')
                        ->get();
                }else {
                    $sucursales = MA_Sucursales::with('gerencia')
                        ->where('Activa', 1)
                        ->where('Visible', 1)
                        ->where('Sucursal', 'like', '%Servicio%')
                        ->orderBy('Sucursal')
                        ->get();
                }
            } else {

                if (!$request->input('data.entrada')) {
                    $sucursales = MA_Sucursales::with('gerencia')
                        ->where('TipoSucursalID', $tipoSucursal)
                        ->where('Activa', 1)
                        ->where('VisibleCC', 1)
                        ->orderBy('Sucursal')
                        ->get();
                } else {
                    $sucursales = MA_Sucursales::with('gerencia')
                        ->where('TipoSucursalID', $tipoSucursal)
                        ->where('Activa', 1)
                        ->where('Visible', 1)
                        ->orderBy('Sucursal')
                        ->get();
                }
            }

        } else {

            if (!$request->input('data.entrada')) {
                $sucursales = MA_Sucursales::with('gerencia')
                    ->where('Activa', 1)
                    ->where('VisibleCC', 1)
                    ->orderBy('Sucursal')
                    ->get();
            } else {
                $sucursales = MA_Sucursales::with('gerencia')
                    ->where('Activa', 1)
                    ->where('Visible', 1)
                    ->orderBy('Sucursal')
                    ->get();
            }
        }

        return new MA_SucursalesCollection($sucursales);
    }
}
