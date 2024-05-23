<?php

namespace App\Filament\Resources\FLU\FLUNotificacionesResource\Pages;

use App\Filament\Resources\FLU\FLUNotificacionesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFLUNotificaciones extends EditRecord
{
    protected static string $resource = FLUNotificacionesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
