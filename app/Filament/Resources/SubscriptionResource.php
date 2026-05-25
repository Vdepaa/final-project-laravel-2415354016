<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Subscriptions';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('service_id')
                    ->relationship('service', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'dismantle' => 'Dismantle',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('service.name')
                    ->label('Service')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'gray' => 'dismantle',
                    ]),
            ])

            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('active')
                        ->label('Active')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(
                            fn (Subscription $record) =>
                            $record->status !== 'active'
                            && $record->status !== 'dismantle'
                        )
                        ->action(function (Subscription $record) {
                            if ($record->status === 'dismantle') {
                                Notification::make()
                                    ->danger()
                                    ->title('Status Locked')
                                    ->body(
                                        'Dismantled subscriptions cannot be changed.'
                                    )
                                    ->send();
                                return;
                            }

                            $record->update([
                                'status' => 'active'
                            ]);
                            Notification::make()
                                ->success()
                                ->title('Subscription Activated')
                                ->send();
                        }),

                    Tables\Actions\Action::make('inactive')
                        ->label('Inactive')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(
                            fn (Subscription $record) =>
                            $record->status !== 'inactive'
                            && $record->status !== 'dismantle'
                        )

                        ->action(function (Subscription $record) {
                            if ($record->status === 'dismantle') {
                                Notification::make()
                                    ->danger()
                                    ->title('Status Locked')
                                    ->body(
                                        'Dismantled subscriptions cannot be changed.'
                                    )
                                    ->send();
                                return;
                            }

                            $record->update([
                                'status' => 'inactive'
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Subscription Inactivated')
                                ->send();
                        }),

                    Tables\Actions\Action::make('dismantle')
                        ->label('Dismantle')
                        ->icon('heroicon-o-trash')
                        ->color('gray')
                        ->visible(
                            fn (Subscription $record) =>
                            $record->status !== 'dismantle'
                        )

                        ->requiresConfirmation()

                        ->action(function (Subscription $record) {
                            $record->update([
                                'status' => 'dismantle'
                            ]);
                            Notification::make()
                                ->success()
                                ->title('Subscription Dismantled')
                                ->body(
                                    'This subscription status is now locked.'
                                )
                                ->send();
                        }),
                ]),

                Tables\Actions\DeleteAction::make()
                    ->before(function (
                        Tables\Actions\DeleteAction $action,
                        Subscription $record
                    ) {
                        if ($record->status !== 'dismantle') {
                            Notification::make()
                                ->danger()
                                ->title('Delete Failed')
                                ->body(
                                    'Only dismantled subscriptions can be deleted.'
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
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
        ];
    }
}