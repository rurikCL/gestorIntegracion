<?php

namespace App\Filament\Resources\VT\VTCotizacionesResource\Pages;

use App\Filament\Resources\VT\VTCotizacionesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVTCotizaciones extends EditRecord
{
    protected static string $resource = VTCotizacionesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
