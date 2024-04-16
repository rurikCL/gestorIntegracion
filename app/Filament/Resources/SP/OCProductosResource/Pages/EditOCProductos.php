<?php

namespace App\Filament\Resources\SP\OCProductosResource\Pages;

use App\Filament\Resources\SP\OCProductosResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOCProductos extends EditRecord
{
    protected static string $resource = OCProductosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
