<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BranchOfficeCollection;
use App\Models\BranchOffice;

class NegocioController extends Controller
{
    public function index()
    {
        return new BranchOfficeCollection(
            BranchOffice::select( 'ID', 'Sucursal')
            ->where( 'Activa', 1)
            ->paginate()
        );
    }
}
