<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MinistryResource\Pages;
use App\Filament\Resources\MinistryResource\RelationManagers;
use App\Models\Ministry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MinistryResource extends Resource
{
    protected static ?string $model = Ministry::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                ->minLength(2)
                ->label('Ministry Name')
                ->required(),
                TextInput::make('ministry_head')
                ->numeric()
                ->required()
                ->label('Ministry Head'),
                Textarea::make('mission') 
                ->label('Mission')
                ->rows(5) 
                ->required(),
                Textarea::make('vision') 
                ->label('Vision')
                ->rows(5) 
                ->required(),
                ToggleButtons::make('status')
                ->options([
                    1 => "Active",
                    0 => "Inactive",
                ])
                ->colors([
                    1 => "success",
                    0 => "danger",
                ])
                ->default(1)
                ->inline()
                ->grouped(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->searchable()
                ->sortable()
                ->label('Ministry Name'),
                TextColumn::make('ministry_head')
                ->searchable()
                ->sortable()
                ->label('Ministry Head'),
                TextColumn::make('status')
                ->label('Active')
                ->color(fn($state) => match($state)
                    {
                        1 => 'success',
                        0 => 'danger'
                    })
                ->formatStateUsing(function($state){
                    if($state)
                    {
                        return 'YES';
                    }
                    else
                    {
                        return 'NO';
                    }
                })
                ->badge(),
                TextColumn::make('mission')
                ->label('Mission')
                ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('vision')
                ->label('Vision')
                ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                ->options(['1' => 'Yes','0' => 'No']),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->label('Edit')
                ->icon('heroicon-s-pencil')
                ->slideOver()
                ->modalWidth('md'),
            DeleteAction::make()
                ->modalHeading(fn($record)=>'Delete Ministry ' . $record->name)
                ->modalIcon('heroicon-s-user')
                ->modalIconColor('info')
                ->modalDescription(fn($record) => 'Are you sure you want to delete ' . $record->first_name . ' ' . $record->last_name . '?' )
                ->label('Delete')
                ->form([
                    DatePicker::make('date_deleted')
                        ->required()
                ])
                ->action(function($record, DeleteAction $action){

                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMinistries::route('/'),
            // 'create' => Pages\CreateMinistry::route('/create'),
            // 'edit' => Pages\EditMinistry::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return Ministry::where('status',1)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success'; // TODO: Change the autogenerated stub
    }
}
