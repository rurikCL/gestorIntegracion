<?php

namespace App\Filament\Resources\API\ApiProveedoresResource\Pages;

use App\Filament\Resources\API\ApiProveedoresResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApiProveedores extends EditRecord
{
    protected static string $resource = ApiProveedoresResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
