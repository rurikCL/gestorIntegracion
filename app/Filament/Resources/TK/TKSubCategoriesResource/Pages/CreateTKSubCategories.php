<?php

namespace App\Filament\Resources\TK\TKSubCategoriesResource\Pages;

use App\Filament\Resources\TK\TKSubCategoriesResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTKSubCategories extends CreateRecord
{
    protected static string $resource = TKSubCategoriesResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['FechaCreacion'] = Carbon::now()->format('Y-m-d H:i:s');
        $data['EventoCreacionID'] = 1;
        $data['UsuarioCreacionID'] = Auth::user()->id;

        $data['SLA'] = match ($data['Prioridad']) {
            'Bajo' => 24,
            'Medio' => 16,
            'Urgente' => 4,
        };

        return $data;
    }
}
