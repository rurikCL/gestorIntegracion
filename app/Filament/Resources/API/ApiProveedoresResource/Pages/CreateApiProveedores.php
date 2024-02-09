<?php

namespace App\Filament\Resources\API\ApiProveedoresResource\Pages;

use App\Filament\Resources\API\ApiProveedoresResource;
use App\Models\Api\ApiProveedores;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateApiProveedores extends CreateRecord
{
    protected static string $resource = ApiProveedoresResource::class;

    protected function handleRecordCreation(array $data): ApiProveedores
    {
        $data['FechaCreacion'] = date("Y-m-d");
        $data['FechaActualizacion'] = date("Y-m-d");
        $data['EventoCreacionID'] = 146;
        $data['EventoActualizacionID'] = 146;
        $data['UsuarioCreacionID'] = auth()->id();
        $data['UsuarioActualizacionID'] = auth()->id();
        return static::getModel()::create($data);
    }
}
