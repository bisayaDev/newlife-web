<?php

namespace App\Filament\Widgets;

use App\Models\Members;
use Filament\Widgets\ChartWidget;

class ActiveInactive extends ChartWidget
{
    protected static ?string $heading = 'Chart';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $active = Members::where('status',1)->count();
        $inactive = Members::where('status',0)->count();
        return [
            'datasets' => [
                [
                    'label' => 'Blog posts created',
                    'data' => [$active,$inactive],
                    'backgroundColor' => [
                        'rgb(54, 162, 235)',
                        'rgb(255, 99, 132)',
                        ],
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => ['Active','InActive'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
