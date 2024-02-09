<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Facades\DB;

class SolicitudesChart extends LineChartWidget
{
    protected static ?string $heading = 'Soliciutdes por mes';

    protected function getData(): array
    {
        $dataOld = \App\Models\Api\ApiSolicitudes::select(DB::raw('count(*) as count'), DB::raw('MONTH(created_at) as month'))
            ->groupBy('month')
            ->whereYear('created_at', Carbon::now()->subYear()->format('Y'))
            ->pluck('count')->toArray();

        $data = \App\Models\Api\ApiSolicitudes::select(DB::raw('count(*) as count'), DB::raw('MONTH(created_at) as month'))
            ->groupBy('month')
            ->whereYear('created_at', Carbon::now()->format('Y'))
            ->pluck('count')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Ultimo año',
                    'data' => $dataOld,
                    'borderColor' => 'rgba(255, 99, 132, 0.8)',
                ],[
                    'label' => 'Año actual',
                    'data' => $data,
                    'borderColor' => 'rgba(54, 162, 235, 0.8)',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }
}
