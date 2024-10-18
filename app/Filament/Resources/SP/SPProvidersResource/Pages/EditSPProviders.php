<?php

namespace App\Filament\Resources\SP\SPProvidersResource\Pages;

use App\Filament\Resources\SP\SPProvidersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSPProviders extends EditRecord
{
    protected static string $resource = SPProvidersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
