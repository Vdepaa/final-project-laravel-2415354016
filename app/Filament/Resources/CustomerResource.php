<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Customers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('name')
                    ->required(),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),

                Forms\Components\TextInput::make('phone')
                    ->required(),

                Forms\Components\Textarea::make('address')
                    ->nullable(),

                Forms\Components\Toggle::make('status')
                    ->default(true),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone'),

                Tables\Columns\IconColumn::make('status')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),

            ])

            ->actions([

                Tables\Actions\EditAction::make(),

                Tables\Actions\ActionGroup::make([

                    Tables\Actions\Action::make('activate')
                        ->label('Activate')
                        ->color('success')
                        ->visible(fn(Customer $record): bool => !$record->status)
                        ->action(function (Customer $record) {

                            $record->update([
                                'status' => true
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Customer Activated')
                                ->send();
                        }),

                    Tables\Actions\Action::make('deactivate')
                        ->label('Deactivate')
                        ->color('danger')
                        ->visible(fn(Customer $record): bool => $record->status)
                        ->action(function (Customer $record) {

                            $record->update([
                                'status' => false
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Customer Deactivated')
                                ->send();
                        }),

                ]),

                Tables\Actions\DeleteAction::make()
                    ->before(function (Tables\Actions\DeleteAction $action, Customer $record) {

                        if ($record->subscriptions()->exists()) {

                            Notification::make()
                                ->danger()
                                ->title('Delete Failed')
                                ->body(
                                    'Customer already has subscriptions.'
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}