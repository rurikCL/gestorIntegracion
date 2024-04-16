<?php

namespace App\Filament\Resources\FLU\FLUHomologacionResource\Pages;

use App\Filament\Resources\FLU\FLUHomologacionResource;
use Carbon\Carbon;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageFLUHomologacions extends ManageRecords
{
    protected static string $resource = FLUHomologacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->mutateFormDataUsing(function (array $data): array {

                $data['FechaCreacion'] = Carbon::now()->format('Y-m-d H:i:s');
                $data['EventoCreacionID'] = 1;
                $data['UsuarioCreacionID'] = 1;

                return $data;
            }),
        ];
    }
}
