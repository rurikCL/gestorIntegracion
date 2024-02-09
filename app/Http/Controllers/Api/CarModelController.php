<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarModelCollection;
use App\Models\CarModel;

class CarModelController extends Controller
{
    public function index()
    {
        return new CarModelCollection(
            CarModel::select('ID', 'Modelo', 'MarcaID')
                ->where('Activo', 1)
                ->paginate()
        );
    }
}
