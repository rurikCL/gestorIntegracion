<?php

namespace App\Filament\Resources\FLU\FLUCargasResource\Pages;

use App\Filament\Resources\FLU\FLUCargasResource;
use App\Http\Controllers\Flujo\FlujoCargaController;
use App\Models\FLU\FLU_Flujos;
use Carbon\Carbon;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ManageFLUCargas extends ManageRecords
{
    protected static string $resource = FLUCargasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {

/*                $data['FechaCreacion'] = Carbon::now()->format('Y-m-d H:i:s');
                $data['EventoCreacionID'] = 1;
                $data['UsuarioCreacionID'] = Auth::user()->id;
                $data['FechaCarga'] = Carbon::now()->format('Y-m-d H:i:s');*/

                return $data;
            })
                ->after(function (array $data) {
                    $flujo = FLU_Flujos::where('ID', $data['ID_Flujo'])->first();
                    if($flujo) {
                        $metodo = $flujo->Metodo;
                        // Ejecuta Metodo de FlujoCargaController
                        Log::info("Ejecutando Metodo de importacion: $metodo");
                        FlujoCargaController::$metodo($data);
                    }

                    return $data;
                }),
        ];
    }

    protected function getHeaderWidgets() : array
    {
        return [FLUCargasResource\Widgets\FluCargas::class];
    }
}
