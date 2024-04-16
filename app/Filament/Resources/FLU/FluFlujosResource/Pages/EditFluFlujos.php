<?php

namespace App\Filament\Resources\FLU\FluFlujosResource\Pages;

use App\Filament\Resources\FLU\FluFlujosResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFluFlujos extends EditRecord
{
    protected static string $resource = FluFlujosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
