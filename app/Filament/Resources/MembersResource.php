<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MembersResource\Pages;
use App\Filament\Resources\MembersResource\RelationManagers;
use App\Models\Members;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\Filter;

class MembersResource extends Resource
{
    protected static ?string $model = Members::class;

    protected static ?string $navigationIcon = 'heroicon-s-users';

    public static function form(Form $form): Form
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

    public static function table(Table $table): Table
    {
        return $table
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
                SelectFilter::make('civil_status')
                    ->searchable()
                    ->multiple()
                    ->options([
                        "single" => "Single",
                        "married" => "Married",
                        "widowed" => "Widowed",
                        "divorced" => "Divorced",
                        "separated" => "Separated"
                    ])

            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-s-pencil')
                        ->slideOver()
                        ->modalWidth('md'),
                    DeleteAction::make()
                        ->modalHeading(fn($record)=>'Delete member ' . $record->first_name)
                        ->modalIcon('heroicon-s-user')
                        ->modalIconColor('info')
                        ->modalDescription(fn($record) => 'Are you sure you want to delete ' . $record->first_name . ' ' . $record->last_name . '????' )
                        ->label('Delete')
                        ->form([
                            DatePicker::make('date_deleted')
                                ->required()
                        ])
                        ->action(function($record, DeleteAction $action){

                        }),
                    Tables\Actions\Action::make('Notify Name')
                        ->form([
                            DatePicker::make('date_send')
                        ])
                        ->action(function($record, Tables\Actions\Action $action){
                            $date_send = $action->getFormData()['date_send'];

                            Notification::make('noty')
                                ->title('The Name')
                                ->success()
                                ->body('Member ' . $record->first_name . ' was send ' . $date_send)
                                ->duration(10000)
                                ->send();
                            })

                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListMembers::route('/'),
//            'create' => Pages\CreateMembers::route('/create'),
//            'edit' => Pages\EditMembers::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return Members::where('status',1)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success'; // TODO: Change the autogenerated stub
    }

}
