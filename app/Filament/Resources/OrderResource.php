<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\AddressRelationManager;
use App\Filament\Resources\OrderResource\Widgets\OrderStats as WidgetsOrderStats;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;
use Livewire\Attributes\Reactive;
use App\Filament\Widgets\OrderStats;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    // protected static ?string $navigationGroup = 'Orders';

    protected static ?int $navigationSort = 2;

    // protected static ?string $recordTitleAttribute = 'payment_method';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Order Information')->schema([
                        Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->options([
                                'QRIS' => 'QRIS',
                                'PayPal' => 'PayPal',
                                'Bank Transfer' => 'Bank Transfer',
                            ])
                            ->required(),

                        Select::make('payment_status')
                            ->label('Payment Status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                            ])
                            ->default('pending')
                            ->required(),

                        ToggleButtons::make('status')
                            ->inline()
                            ->default('new')
                            ->required()
                            ->label('Order Status')
                            ->options([
                                'new' => 'New',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                            ])
                            ->colors([
                                'new' => 'info',
                                'processing' => 'warning',
                                'shipped' => 'success',
                                'delivered' => 'success',
                                'cancelled' => 'danger',
                            ])
                            ->icons([
                                'new' => 'heroicon-m-sparkles',
                                'processing' => 'heroicon-m-arrow-path',
                                'shipped' => 'heroicon-m-truck',
                                'delivered' => 'heroicon-m-check-badge',
                                'cancelled' => 'heroicon-m-x-circle',
                            ]),

                        Select::make('currency')
                            ->label('Currency')
                            ->options([
                                'IDR' => 'IDR',
                                'USD' => 'USD',
                                'EUR' => 'EUR',
                            ])
                            ->default('IDR')
                            ->required(),

                        Select::make('shipping_method')
                            ->required()
                            ->label('Shipping Method')
                            ->options([
                                'fedex' => 'Fed Express (Luar Negeri)',
                                'dhl' => 'DHL (Luar Negeri)',
                                'pos' => 'Pos Indonesia (Luar & Dalam Negeri)',
                                'jnt' => 'JNT (Dalam Negeri)',
                                'jne' => 'JNE (Dalam Negeri)',
                            ]),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->columnSpanFull()
                    ])->columns(2),

                    Section::make('Order Items')->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->distinct()
                                    ->columnSpan(4)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $product = Product::find($state);
                                        $price = $product?->price ?? 0;
                                        $set('unit_amount', $price);
                                        $set('total_amount', $price * ($get('quantity') ?? 1));
                                        self::updateGrandTotal($get, $set);
                                    })
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->columnSpan(2)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $unitAmount = $get('unit_amount') ?? 0;
                                        $set('total_amount', $state * $unitAmount);
                                        self::updateGrandTotal($get, $set);
                                    }),

                                TextInput::make('unit_amount')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(3),

                                TextInput::make('total_amount')
                                    ->numeric()
                                    ->required()
                                    ->dehydrated()
                                    ->columnSpan(3)
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, Set $set, Get $get) => self::updateGrandTotal($get, $set)),
                            ])
                            ->columns(12)
                            ->afterStateUpdated(fn ($state, Set $set, Get $get) => self::updateGrandTotal($get, $set))
                            ->deleteAction(
                                fn ($action) => $action->after(fn (Set $set, Get $get) => self::updateGrandTotal($get, $set))
                            ),

                        Placeholder::make('grand_total_placeholder')
                            ->label('Grand Total')
                            ->content(function (Get $get) {
                                $total = 0;
                                if (!$repeaters = $get('items')) {
                                    return Number::currency($total, 'IDR');
                                }
                                foreach ($repeaters as $key => $repeater) {
                                    $total += $get("items.{$key}.total_amount") ?? 0;
                                }
                                return Number::currency($total, 'IDR');
                            }),

                        Hidden::make('grand_total')
                            ->default(0)
                            ->dehydrated(),
                    ])
                ])->columnSpanFull(),
            ]);
    }

    // Function helper untuk update grand total
    protected static function updateGrandTotal(Get $get, Set $set): void
    {
        $total = 0;
        if ($repeaters = $get('items')) {
            foreach ($repeaters as $key => $repeater) {
                $total += $get("items.{$key}.total_amount") ?? 0;
            }
        }
        $set('grand_total', $total);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('user.name')
                ->label('Customer')
                ->searchable(),

            // --- PERBAIKAN UTAMA ADA DI SINI ---
            TextColumn::make('grand_total')
                ->label('Totals')
                ->numeric()
                ->sortable()
                ->money('IDR')
                ->getStateUsing(function ($record) {
                    // Selalu hitung ulang total dari relasi 'items' untuk data realtime.
                    // Ini memastikan nilai yang ditampilkan di tabel selalu akurat.
                    return $record->items->sum('total_amount');
                }),

            TextColumn::make('payment_method')
                ->searchable(),

            TextColumn::make('payment_status')
                ->searchable()
                ->sortable(),

            TextColumn::make('shipping_method')
                ->searchable(),

            SelectColumn::make('status')
                ->options([
                    'new' => 'New',
                    'processing' => 'Processing',
                    'shipped' => 'Shipped',
                    'delivered' => 'Delivered',
                    'cancelled' => 'Cancelled',
                ])
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            //
        ])
        ->actions([
            Tables\Actions\ActionGroup::make([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
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
            AddressRelationManager::class
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? 'success' : 'danger';
    }

    public static function getWidgets(): array
        {
            return [
                WidgetsOrderStats::class, // <--- PASTIKAN HANYA INI YANG ADA DI SINI
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
}
