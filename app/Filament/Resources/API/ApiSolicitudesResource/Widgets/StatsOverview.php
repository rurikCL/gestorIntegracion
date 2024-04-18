<?php

namespace App\Filament\Resources\API\ApiSolicitudesResource\Widgets;

use App\Models\Api\ApiSolicitudes;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverview extends BaseWidget
{

    protected static ?int $sort = 0;
    protected function getCards(): array
    {
        return [
            //
//            Card::make('Unique views', '192.1k')
//                ->description('32k increase')
//                ->descriptionIcon('heroicon-s-trending-up')
//                ->chart([7, 2, 10, 3, 15, 4, 17])
//                ->color('success'),
            Card::make('Trabajos pendientes', ApiSolicitudes::SolicitudesPendientes())
            ->description(date('d-m-Y H:i:s'))
                ->color('primary'),
            Card::make('Trabajos exitosos',
                ApiSolicitudes::SolicitudesExitosas())
                ->description('Fallidas : '.ApiSolicitudes::SolicitudesFallidas().
                    " - Total : ". ApiSolicitudes::SolicitudesListas()
                )
                ->color('warning'),
            Card::make('Ultimo trabajo procesado', ApiSolicitudes::UltimaResolucion())
        ];
    }
}
