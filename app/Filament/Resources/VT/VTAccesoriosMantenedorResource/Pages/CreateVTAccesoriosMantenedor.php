<?php

namespace App\Filament\Resources\VT\VTAccesoriosMantenedorResource\Pages;

use App\Filament\Resources\VT\VTAccesoriosMantenedorResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateVTAccesoriosMantenedor extends CreateRecord
{
    protected static string $resource = VTAccesoriosMantenedorResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['FechaCreacion'] = Carbon::now()->format('Y-m-d H:i:s');
        $data['EventoCreacionID'] = 1;
        $data['UsuarioCreacionID'] = Auth::user()->id;

        return $data;
    }
}
