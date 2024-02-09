<?php

namespace App\Http\Controllers\ApiProd;

use App\Http\Controllers\Controller;
use App\Http\Resources\MA\MA_MarcasCollection;
use App\Models\MA\MA_Marcas;
use Illuminate\Http\Request;

class MarcasController extends Controller
{
    public function index()
    {
        return new MA_MarcasCollection(
            MA_Marcas::get()
        );
    }
}
