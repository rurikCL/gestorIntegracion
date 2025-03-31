<?php

namespace App\Filament\Resources\FLU\FLUMonitorResource\Pages;

use App\Filament\Resources\FLU\FLUMonitorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFLUMonitor extends EditRecord
{
    protected static string $resource = FLUMonitorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
