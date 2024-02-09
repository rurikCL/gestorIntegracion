<?php

namespace App\Filament\Resources\EntidadesFinancierasResource\Pages;

use App\Filament\Resources\EntidadesFinancierasResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageEntidadesFinancieras extends ManageRecords
{
    protected static string $resource = EntidadesFinancierasResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
