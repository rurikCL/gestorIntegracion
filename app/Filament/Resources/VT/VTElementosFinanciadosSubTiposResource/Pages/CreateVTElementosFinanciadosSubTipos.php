<?php

namespace App\Filament\Resources\VT\VTElementosFinanciadosSubTiposResource\Pages;

use App\Filament\Resources\VT\VTElementosFinanciadosSubTiposResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateVTElementosFinanciadosSubTipos extends CreateRecord
{
    protected static string $resource = VTElementosFinanciadosSubTiposResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['FechaCreacion'] = Carbon::now()->format('Y-m-d H:i:s');
        $data['EventoCreacionID'] = 1;
        $data['UsuarioCreacionID'] = Auth::user()->id;

        return $data;
    }
}
