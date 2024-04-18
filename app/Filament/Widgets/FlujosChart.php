<?php

namespace App\Filament\Widgets;

use App\Models\Api\ApiSolicitudes;
use App\Models\FLU\FLU_Flujos;
use Filament\Widgets\PieChartWidget;
use Illuminate\Support\Facades\DB;

class FlujosChart extends PieChartWidget
{
    protected static ?string $heading = 'Solicitudes por flujo';
    protected static ?int $sort = 1;
    protected function getData(): array
    {
        $flujos = FLU_Flujos::select('ID', 'Nombre')->get();

        $colores = [
            '#AA7BD956',
            '#AA8C625A',
            '#AA6B82E7',
            '#AA520ACC',
            '#AAA70762',
            '#AA6879C0',
            '#AADF6AFF',
            '#AAA6684D',
            '#AA186296',
            '#AAF591BE',
            '#AACA026C',
            '#AA8173A5',
            '#AABA6727',
            '#AAA8915B',
            '#AAA763A6',
            '#AA83032A',
            '#AA959689',
            '#AA62EF00',
            '#AAF176F7',
            '#AAA7D9F5'
        ];

        foreach ($flujos as $flujo) {
            $data[] = ApiSolicitudes::where('FlujoID', $flujo->ID)->count();
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $colores,
                    'borderColor'=> '#fff',
                ]
            ],
            'labels' => $flujos->pluck('Nombre')->toArray(),
        ];
    }
}
