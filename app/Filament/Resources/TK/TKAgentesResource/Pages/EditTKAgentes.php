<?php

namespace App\Filament\Resources\TK\TKAgentesResource\Pages;

use App\Filament\Resources\TK\TKAgentesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTKAgentes extends EditRecord
{
    protected static string $resource = TKAgentesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
