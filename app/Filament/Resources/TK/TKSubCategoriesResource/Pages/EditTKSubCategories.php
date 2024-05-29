<?php

namespace App\Filament\Resources\TK\TKSubCategoriesResource\Pages;

use App\Filament\Resources\TK\TKSubCategoriesResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditTKSubCategories extends EditRecord
{
    protected static string $resource = TKSubCategoriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['SLA'] = match ($data['Prioridad']) {
            'Bajo' => 24,
            'Medio' => 16,
            'Urgente' => 4,
        };

        return $data;
    }
}
