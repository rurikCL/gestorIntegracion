<?php

namespace App\Filament\Resources\VT\VTVentasResource\Pages;

use App\Filament\Resources\VT\VTVentasResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVTVentas extends EditRecord
{
    protected static string $resource = VTVentasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
