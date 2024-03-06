<?php

namespace App\Filament\Resources\TK\TKAgentesResource\Pages;

use App\Filament\Resources\TK\TKAgentesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTKAgentes extends ListRecords
{
    protected static string $resource = TKAgentesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
