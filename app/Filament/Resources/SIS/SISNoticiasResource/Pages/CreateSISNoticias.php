<?php

namespace App\Filament\Resources\SIS\SISNoticiasResource\Pages;

use App\Filament\Resources\SIS\SISNoticiasResource;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateSISNoticias extends CreateRecord
{
    protected static string $resource = SISNoticiasResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $data['FechaCreacion'] = Carbon::now()->format('Y-m-d H:i:s');
        $data['EventoCreacionID'] = 1;
        $data['UsuarioCreacionID'] = Auth::user()->id;

        return $data;
    }
}
