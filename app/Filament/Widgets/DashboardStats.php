<?php

namespace App\Filament\Widgets;

use App\Models\Members;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Active Member',Members::where('status',1)->count())
                ->description('All active members')
                ->chart([2,5,2,5,3])
                ->chartColor('success'),
            Stat::make('Not Active Member',Members::where('status',0)->count())
                ->description('All active members')
                ->chart([4,5,1,2,3])
                ->color('danger'),
        ];
    }
}
