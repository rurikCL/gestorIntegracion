<?php

namespace App\Filament\Resources\FLU\FluFlujosResource\Pages;

use App\Filament\Resources\FLU\FluFlujosResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFluFlujos extends ListRecords
{
    protected static string $resource = FluFlujosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
