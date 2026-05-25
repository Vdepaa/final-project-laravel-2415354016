<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Service';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->minValue(0),

                Forms\Components\Textarea::make('description')
                    ->nullable()
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('status')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\IconColumn::make('status')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])

            ->actions([

                Tables\Actions\EditAction::make(),

                Tables\Actions\ActionGroup::make([

                    Tables\Actions\Action::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn(Service $record): bool => !$record->status)
                        ->action(function (Service $record) {

                            $record->update([
                                'status' => true
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Service Activated')
                                ->send();
                        }),

                    Tables\Actions\Action::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn(Service $record): bool => $record->status)
                        ->action(function (Service $record) {

                            $record->update([
                                'status' => false
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Service Deactivated')
                                ->send();
                        }),

                ]),

                Tables\Actions\DeleteAction::make()
                    ->before(function (Tables\Actions\DeleteAction $action, Service $record) {

                        if ($record->subscriptions()->exists()) {

                            Notification::make()
                                ->danger()
                                ->title('Delete Failed')
                                ->body(
                                    'Service already has subscriptions.'
                                )
                                ->send();

                            $action->halt();
                        }
                    }),

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}