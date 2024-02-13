<?php

namespace App\Filament\Resources\SP\OCSubCategoriasResource\Pages;

use App\Filament\Resources\SP\OCSubCategoriasResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOCSubCategorias extends EditRecord
{
    protected static string $resource = OCSubCategoriasResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
