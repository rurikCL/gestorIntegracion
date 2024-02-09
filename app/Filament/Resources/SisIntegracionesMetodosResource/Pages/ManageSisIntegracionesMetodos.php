<?php

namespace App\Filament\Resources\SisIntegracionesMetodosResource\Pages;

use App\Filament\Resources\SisIntegracionesMetodosResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSisIntegracionesMetodos extends ManageRecords
{
    protected static string $resource = SisIntegracionesMetodosResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
