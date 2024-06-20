<?php

namespace App\Filament\Widgets;

use App\Models\Ministry;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MinistryStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Active Ministry',Ministry::where('status',1)->count())
            ->description('All active ministries')
            ->chartColor('success'),
        Stat::make('Not Active Member',Ministry::where('status',0)->count())
            ->description('All active ministries')
            ->color('danger'),
        ];
    }
}
