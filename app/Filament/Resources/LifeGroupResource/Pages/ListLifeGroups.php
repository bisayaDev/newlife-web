<?php

namespace App\Filament\Resources\LifeGroupResource\Pages;

use App\Filament\Resources\LifeGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLifeGroups extends ListRecords
{
    protected static string $resource = LifeGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('New LifeGroup')
            ->icon('heroicon-s-user-plus'),
        ];
    }
}
