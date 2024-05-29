<?php

namespace App\Filament\Resources\TK\TKSubCategoriesResource\Pages;

use App\Filament\Resources\TK\TKSubCategoriesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTKSubCategories extends ListRecords
{
    protected static string $resource = TKSubCategoriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
