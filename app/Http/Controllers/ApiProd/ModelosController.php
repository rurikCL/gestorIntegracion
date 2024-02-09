<?php

namespace App\Http\Controllers\ApiProd;

use App\Http\Controllers\Controller;
use App\Http\Resources\MA\MA_ModelosCollection;
use App\Models\MA\MA_Gerencias;
use App\Models\MA\MA_Modelos;
use Illuminate\Http\Request;

class ModelosController extends Controller
{
    public function index(Request $request)
    {

        $idMarca = $request->input('data.id_marca');

        $gerencia = MA_Gerencias::where("ID", $idMarca)
            ->first();

        $modelos = MA_Modelos::with('marca')
            ->where('MarcaID', $gerencia->MarcaAsociada ?? $request->input('data.id_marca'))
            ->where('Activo', 1)
            ->where('ActivoNuevo', 1)
            ->orderBy('Modelo', 'Asc')
            ->get();

        if ($request->input('data.id_marca')) {
            return new MA_ModelosCollection(
                $modelos
            );
        } else {
            return new MA_ModelosCollection(
                MA_Modelos::with('marca')
                    ->where('Activo', 1)
                    ->where('ActivoUsados', 1)
                    ->orderBy('Modelo', 'Asc')
                    ->get()
            );
        }

    }
}
