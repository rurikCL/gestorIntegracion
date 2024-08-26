<?php

namespace App\Filament\Resources\VT\VTElementosFinanciadosSubTiposResource\Pages;

use App\Filament\Resources\VT\VTElementosFinanciadosSubTiposResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVTElementosFinanciadosSubTipos extends ListRecords
{
    protected static string $resource = VTElementosFinanciadosSubTiposResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
