<?php

namespace App\Filament\Resources\MA\MAModelosResource\Pages;

use App\Filament\Resources\MA\MAModelosResource;
use Carbon\Carbon;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateMAModelos extends CreateRecord
{
    protected static string $resource = MAModelosResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $data['FechaCreacion'] = Carbon::now()->format('Y-m-d H:i:s');
        $data['EventoCreacionID'] = 1;
        $data['UsuarioCreacionID'] = Auth::user()->id;
        return $data;
    }
}
