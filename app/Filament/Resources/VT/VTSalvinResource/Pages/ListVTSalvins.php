<?php

namespace App\Filament\Resources\VT\VTSalvinResource\Pages;

use App\Filament\Resources\VT\VTSalvinResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVTSalvins extends ListRecords
{
    protected static string $resource = VTSalvinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
