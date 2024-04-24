<?php

namespace App\Filament\Resources\MA\MAUsuariosResource\Pages;

use App\Filament\Resources\MA\MAUsuariosResource;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateMAUsuarios extends CreateRecord
{
    protected static string $resource = MAUsuariosResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['FechaCreacion'] = Carbon::now()->format('Y-m-d H:i:s');
        $data['EventoCreacionID'] = 1;
        $data['UsuarioCreacionID'] = Auth::user()->id;
        $data['DetalleID'] = 1;

        return $data;
    }
}
