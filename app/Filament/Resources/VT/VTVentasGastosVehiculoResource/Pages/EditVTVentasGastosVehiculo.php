<?php

namespace App\Filament\Resources\VT\VTVentasGastosVehiculoResource\Pages;

use App\Filament\Resources\VT\VTVentasGastosVehiculoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVTVentasGastosVehiculo extends EditRecord
{
    protected static string $resource = VTVentasGastosVehiculoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
