<?php

namespace App\Filament\Resources\VT\VTSalvinResource\Pages;

use App\Filament\Resources\VT\VTSalvinResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVTSalvin extends EditRecord
{
    protected static string $resource = VTSalvinResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
