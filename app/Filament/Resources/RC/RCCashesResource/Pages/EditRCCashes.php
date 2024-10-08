<?php

namespace App\Filament\Resources\RC\RCCashesResource\Pages;

use App\Filament\Resources\RC\RCCashesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRCCashes extends EditRecord
{
    protected static string $resource = RCCashesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
