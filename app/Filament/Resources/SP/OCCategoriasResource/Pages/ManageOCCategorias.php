<?php

namespace App\Filament\Resources\SP\OCCategoriasResource\Pages;

use App\Filament\Resources\SP\OCCategoriasResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageOCCategorias extends ManageRecords
{
    protected static string $resource = OCCategoriasResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
