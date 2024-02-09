<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OriginLeadCollection;
use App\Models\OriginLead;

class OriginLeadController extends Controller
{
    public function index()
    {
        return new OriginLeadCollection(
            OriginLead::select( 'ID', 'Origen')
            ->paginate()
        );
    }
}
