<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\TextInput::make('number')
                            ->label('Order Number')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'canceled' => 'Canceled',
                                'shipped' => 'Shipped',
                                'completed' => 'Completed',
                            ])
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('payment_method')
                            ->label('Payment Method')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('payment_reference')
                            ->label('Payment Reference')
                            ->disabled()
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Shipping Information')
                    ->schema([
                        Forms\Components\TextInput::make('shipping_name')
                            ->label('Name')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('shipping_phone')
                            ->label('Phone')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\Textarea::make('shipping_street')
                            ->label('Street Address')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('shipping_city')
                            ->label('City')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('shipping_province')
                            ->label('Province')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('shipping_postal_code')
                            ->label('Postal Code')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('shipping_method')
                            ->label('Shipping Method')
                            ->disabled()
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Order Totals')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->prefix('Rp')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('shipping_total')
                            ->label('Shipping')
                            ->prefix('Rp')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('discount_total')
                            ->label('Discount')
                            ->prefix('Rp')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('grand_total')
                            ->label('Grand Total')
                            ->prefix('Rp')
                            ->disabled()
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Order Notes')
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Guest'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'shipped' => 'info',
                        'completed' => 'success',
                        'failed' => 'danger',
                        'canceled' => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('grand_total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment')
                    ->placeholder('Not specified'),
                Tables\Columns\TextColumn::make('shipping_city')
                    ->label('Ship To')
                    ->placeholder('Not specified'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'canceled' => 'Canceled',
                        'shipped' => 'Shipped',
                        'completed' => 'Completed',
                    ])
                    ->multiple(),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Order Date From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Order Date Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (Order $record): bool => $record->status !== 'completed'),
                Tables\Actions\Action::make('mark_paid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn (Order $record) => $record->update(['status' => 'paid']))
                    ->visible(fn (Order $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Mark Order as Paid')
                    ->modalDescription('Are you sure you want to mark this order as paid?'),
                Tables\Actions\Action::make('mark_shipped')
                    ->label('Mark as Shipped')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->action(fn (Order $record) => $record->update(['status' => 'shipped']))
                    ->visible(fn (Order $record): bool => $record->status === 'paid')
                    ->requiresConfirmation()
                    ->modalHeading('Mark Order as Shipped')
                    ->modalDescription('Are you sure you want to mark this order as shipped?'),
                Tables\Actions\Action::make('mark_completed')
                    ->label('Mark as Completed')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->action(fn (Order $record) => $record->update(['status' => 'completed']))
                    ->visible(fn (Order $record): bool => $record->status === 'shipped')
                    ->requiresConfirmation()
                    ->modalHeading('Mark Order as Completed')
                    ->modalDescription('Are you sure you want to mark this order as completed?'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(function (): bool {
                            $user = Auth::user();
                            return $user ? $user->can('delete_orders') : false;
                        }),
                    Tables\Actions\BulkAction::make('mark_shipped')
                        ->label('Mark as Shipped')
                        ->icon('heroicon-o-truck')
                        ->color('info')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                if ($record->status === 'paid') {
                                    $record->update(['status' => 'shipped']);
                                }
                            });
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Mark Selected Orders as Shipped')
                        ->modalDescription('This will mark all selected paid orders as shipped.'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Order Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('number')
                            ->label('Order Number'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'paid' => 'success',
                                'shipped' => 'info',
                                'completed' => 'success',
                                'failed' => 'danger',
                                'canceled' => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Customer')
                            ->placeholder('Guest Order'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Order Date')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('payment_method')
                            ->label('Payment Method')
                            ->placeholder('Not specified'),
                        Infolists\Components\TextEntry::make('payment_reference')
                            ->label('Payment Reference')
                            ->placeholder('Not specified'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Shipping Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('shipping_name')
                            ->label('Recipient Name'),
                        Infolists\Components\TextEntry::make('shipping_phone')
                            ->label('Phone Number'),
                        Infolists\Components\TextEntry::make('shipping_street')
                            ->label('Street Address')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('shipping_city')
                            ->label('City'),
                        Infolists\Components\TextEntry::make('shipping_province')
                            ->label('Province'),
                        Infolists\Components\TextEntry::make('shipping_postal_code')
                            ->label('Postal Code'),
                        Infolists\Components\TextEntry::make('shipping_method')
                            ->label('Shipping Method'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Order Items')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->schema([
                                Infolists\Components\TextEntry::make('product_name')
                                    ->label('Product'),
                                Infolists\Components\TextEntry::make('price')
                                    ->label('Price')
                                    ->money('IDR'),
                                Infolists\Components\TextEntry::make('qty')
                                    ->label('Quantity'),
                                Infolists\Components\TextEntry::make('subtotal')
                                    ->label('Subtotal')
                                    ->money('IDR'),
                            ])
                            ->columns(4),
                    ]),

                Infolists\Components\Section::make('Order Summary')
                    ->schema([
                        Infolists\Components\TextEntry::make('subtotal')
                            ->label('Subtotal')
                            ->money('IDR'),
                        Infolists\Components\TextEntry::make('shipping_total')
                            ->label('Shipping')
                            ->money('IDR'),
                        Infolists\Components\TextEntry::make('discount_total')
                            ->label('Discount')
                            ->money('IDR'),
                        Infolists\Components\TextEntry::make('grand_total')
                            ->label('Grand Total')
                            ->money('IDR')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight('bold'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Notes')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Order Notes')
                            ->placeholder('No notes')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getNavigationBadge() > 0 ? 'warning' : null;
    }
}
