<?php

namespace App\Http\Controllers\ApiProd;

use App\Http\Controllers\Controller;
use App\Http\Resources\MA\MA_GerenciasCollection;
use App\Models\MA\MA_Gerencias;
use Illuminate\Http\Request;

class GerenciasController extends Controller
{
    public function index()
    {
        return new MA_GerenciasCollection(
            MA_Gerencias::where('Activo', 1)
//                ->where('Visible', 1)
                ->where('MarcaAsociada', '>', 0)
                ->orWhere('ID', 4)
                ->orderBy('Gerencia', 'Asc')
                ->get()
        );
    }
}
