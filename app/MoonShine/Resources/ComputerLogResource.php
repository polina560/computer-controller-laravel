<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\ComputerLog;
use MoonShine\Laravel\Components\Fragment;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\Column;

class ComputerLogResource extends ModelResource
{
    protected string $model = ComputerLog::class;
    protected string $title = 'Статус компьютеров';
    protected bool $createInModal = true;
    protected bool $editInModal = true;
    protected array $with = ['computer'];

    public function indexComponents(): array
    {
        return [
            Grid::make([
                Column::make([
                    $this->getChartFragment(),
                ])->columnSpan(12),
            ]),
        ];
    }

    protected function getChartFragment(): Fragment
    {
        return Fragment::make([
            view('moonshine.components.status-chart', [
                'chartData' => $this->getChartData()
            ])->render()
        ]);
    }

    protected function getChartData(): array
    {
        try {
            return ComputerLog::query()
                ->with(['computer' => fn($q) => $q->select('id', 'name')])
                ->where('created_at', '>=', now()->subDays(7))
                ->orderBy('created_at')
                ->get()
                ->groupBy('computer_id')
                ->map(function ($logs, $computerId) {
                    $computer = $logs->first()->computer;

                    return [
                        'name' => $computer->name ?? 'Computer '.$computerId,
                        'data' => $logs->map(fn($log) => [
                            'x' => $log->created_at->format('Y-m-d H:i:s'),
                            'y' => (int)$log->status,
                            'fillColor' => $log->status ? '#10B981' : '#EF4444'
                        ])->toArray()
                    ];
                })
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            report($e);
            return [];
        }
    }

    public function filters(): array { return []; }
    public function rules($item): array { return []; }
}
