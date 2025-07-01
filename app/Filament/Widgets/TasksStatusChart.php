<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Widgets\ChartWidget;

class TasksStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Tasks';

    protected function getData(): array
    {
        $data = Task::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $labels = ['completed', 'pending', 'cancelled'];

        $colors = [
            'completed' => '#22c55e',   // verde
            'pending'   => '#eab308',   // galben
            'cancelled' => '#ef4444',   // roșu
        ];

        return [
            'datasets' => [
                [
                    'data' => array_map(fn ($label) => $data[$label] ?? 0, $labels),
                    'backgroundColor' => array_map(fn ($label) => $colors[$label] ?? 0, $labels),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
