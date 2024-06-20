<?php

namespace App\Filament\Resources\LifeGroupResource\Pages;

use App\Filament\Resources\LifeGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLifeGroup extends EditRecord
{
    protected static string $resource = LifeGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
