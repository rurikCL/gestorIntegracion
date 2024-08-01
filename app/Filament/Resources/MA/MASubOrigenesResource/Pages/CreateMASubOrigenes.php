<?php

namespace App\Filament\Resources\MA\MASubOrigenesResource\Pages;

use App\Filament\Resources\MA\MASubOrigenesResource;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateMASubOrigenes extends CreateRecord
{
    protected static string $resource = MASubOrigenesResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $data['FechaCreacion'] = Carbon::now()->format('Y-m-d H:i:s');
        $data['EventoCreacionID'] = 1;
        $data['UsuarioCreacionID'] = Auth::user()->id;
        return $data;
    }
}
