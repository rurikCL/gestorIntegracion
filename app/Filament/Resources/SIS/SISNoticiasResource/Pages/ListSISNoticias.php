<?php

namespace App\Filament\Resources\SIS\SISNoticiasResource\Pages;

use App\Filament\Resources\SIS\SISNoticiasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSISNoticias extends ListRecords
{
    protected static string $resource = SISNoticiasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
