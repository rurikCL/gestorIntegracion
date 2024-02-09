<?php

namespace App\Filament\Resources\SisIntegracionesResource\Pages;

use App\Filament\Resources\SisIntegracionesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSisIntegraciones extends ManageRecords
{
    protected static string $resource = SisIntegracionesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
