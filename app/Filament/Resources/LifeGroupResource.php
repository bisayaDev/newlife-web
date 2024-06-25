<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LifeGroupResource\Pages;
use App\Filament\Resources\LifeGroupResource\RelationManagers;
use App\Models\LifeGroup;
use App\Models\Members;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DatetimePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;


class LifeGroupResource extends Resource
{
    protected static ?string $model = LifeGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Name'),
                Select::make('leader')
                    ->options(Members::all()->pluck('first_name','id'))
                    ->label('Leader Name'),
                DatePicker::make('schedule')
                    ->label('Date Started')
                    ->default(now()),
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
                    ->grouped()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('LifeGroup Name')
                    ->sortable(),
                TextColumn::make('leader')
                    ->label('LifeGroup Leader')
                    ->formatStateUsing(fn($state) => Members::find($state)->first_name . ' ' . Members::find($state)->last_name)
                    ->sortable(),
                TextColumn::make('schedule')
                    ->label('Date Started')
                    ->sortable()
                    ->formatStateUsing(fn($state) => date_format(Carbon::make($state), 'M d, Y')),
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
            ])
            ->filters([
                Selectfilter::make('name')
                ->searchable()
                ->multiple()
                ->label('Leader'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MembersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLifeGroups::route('/'),
//            'create' => Pages\CreateLifeGroup::route('/create'),
            'edit' => Pages\EditLifeGroup::route('/{record}/edit'),
        ];
    }
}
