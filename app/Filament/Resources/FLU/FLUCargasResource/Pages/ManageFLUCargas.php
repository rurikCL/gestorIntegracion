<?php

namespace App\Filament\Resources\FLU\FLUCargasResource\Pages;

use App\Filament\Resources\FLU\FLUCargasResource;
use App\Http\Controllers\Flujo\FlujoCargaController;
use Carbon\Carbon;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;

class ManageFLUCargas extends ManageRecords
{
    protected static string $resource = FLUCargasResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()->mutateFormDataUsing(function (array $data): array {

                $data['FechaCreacion'] = Carbon::now()->format('Y-m-d H:i:s');
                $data['EventoCreacionID'] = 1;
                $data['UsuarioCreacionID'] = Auth::user()->id;
                $data['FechaCarga'] = Carbon::now()->format('Y-m-d H:i:s');

                return $data;
            })
                ->after(function (array $data) {
                    if($data['ID_Flujo'] == 10)
                        $importacion = FlujoCargaController::importLeads($data);
                    else if($data['ID_Flujo'] == 18)
                        $importacion = FlujoCargaController::importSalvins($data);
                    else if($data['ID_Flujo'] == 20)
                        $importacion = FlujoCargaController::importTransactionAutored($data);
                    else
                        $importacion = FlujoCargaController::importCotizaciones($data);

                    return $data;
                }),
        ];
    }

    protected function getHeaderWidgets() : array
    {
        return [FLUCargasResource\Widgets\FluCargas::class];
    }
}
