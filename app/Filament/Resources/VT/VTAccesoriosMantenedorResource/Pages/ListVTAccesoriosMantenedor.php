<?php

namespace App\Filament\Resources\VT\VTAccesoriosMantenedorResource\Pages;

use App\Filament\Resources\VT\VTAccesoriosMantenedorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVTAccesoriosMantenedor extends ListRecords
{
    protected static string $resource = VTAccesoriosMantenedorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
