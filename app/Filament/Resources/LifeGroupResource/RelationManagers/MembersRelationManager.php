<?php

namespace App\Filament\Resources\LifeGroupResource\RelationManagers;

use App\Models\LifeGroup;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('first_name')
                    ->minLength(2)
                    ->alpha()
                    ->label('First Name')
                    ->required(),
                TextInput::make('last_name')
                    ->required()
                    ->label('Last Name'),
                DatePicker::make('birthday')
                    ->default(now())
                    ->required(),
                Select::make('gender')
                    ->required()
                    ->options([
                        "male" => "Male",
                        "female" => "Female"
                    ]),
                Select::make('civil_status')
                    ->options([
                        "single" => "Single",
                        "married" => "Married",
                        "widowed" => "Widowed",
                        "divorced" => "Divorced",
                        "separated" => "Separated"
                    ]),
                Select::make('life_group_id')
                    ->options(LifeGroup::all()->pluck('name','id'))
                    ->label('LifeGroup'),
                Forms\Components\Section::make('Contact Details')
                    ->schema([
                        TextInput::make('contact'),
                        TextInput::make('facebook'),
                        TextInput::make('email')->label('E-mail')
                            ->unique(ignoreRecord: true)
                            ->email(),
                    ]),
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
                TextInput::make('address')

            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('first_name')
            ->columns([
                TextColumn::make('first_name')
                    ->label('Fullname')
                    ->formatStateUsing(function($record){
                        return $record->last_name . ', ' . $record->first_name ;
                    }),
                TextColumn::make('birthday')
                    ->sortable()
                    ->formatStateUsing(fn($state) => date_format(Carbon::make($state), 'M d, Y')),
                TextColumn::make('age')
                    ->default(0)
                    ->formatStateUsing(fn($record)=>Carbon::make($record->birthday)->age),
                TextColumn::make('gender')
                    ->formatStateUsing(fn($state) => ucfirst($state)),
                TextColumn::make('civil_status')
                    ->formatStateUsing(fn($state) => ucfirst($state)),
                TextColumn::make('contact'),
                TextColumn::make('facebook')
                    ->url(fn($state) => 'https://' . $state)
                    ->color('info')
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')
                    ->url(fn($state) =>'mailto:' . $state)
                    ->toggleable(),
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
                TextColumn::make('address'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
//                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
