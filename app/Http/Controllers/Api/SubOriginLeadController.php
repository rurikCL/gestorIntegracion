<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubOriginLeadCollection;
use App\Models\SubOriginLead;

class SubOriginLeadController extends Controller
{
    public function index()
    {
        return new SubOriginLeadCollection(
            SubOriginLead::select( 'ID', 'SubOrigen', 'OrigenID' )
            ->paginate()
        );
    }
}
