<?php

namespace App\Filament\Resources\SisIntegracionesHeadersResource\Pages;

use App\Filament\Resources\SisIntegracionesHeadersResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSisIntegracionesHeaders extends ManageRecords
{
    protected static string $resource = SisIntegracionesHeadersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
