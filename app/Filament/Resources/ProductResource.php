<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Catalogs';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Product Details')->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Set $set) {
                                if ($operation !== 'create') {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated()
                            ->unique(Product::class, 'slug', ignoreRecord: true),

                        MarkdownEditor::make('description')
                            ->required()
                            ->columnSpanFull()
                            ->fileAttachmentsDirectory('products'),
                    ])->columns(2),

                    Section::make('Images')->schema([
                        FileUpload::make('image')
                            ->label('Product Images')
                            ->multiple()
                            ->required()
                            ->maxFiles(10)
                            ->reorderable()
                            ->directory('products')
                            ->image()
                            ->imageEditor()
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('800')
                            ->imageResizeTargetHeight('800')
                            ->columnSpanFull(),
                    ])
                ])->columnspan(2),

                Group::make()->schema([
                    Section::make('Pricing')->schema([
                        TextInput::make('price')
                            ->label('Harga Asli')
                            ->required()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('IDR')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                // Auto update sale price when on_sale is true
                                if ($get('on_sale') && !$get('sale_price') && $state) {
                                    $numericPrice = is_string($state) ? (float) str_replace([',', 'IDR', ' '], '', $state) : (float) $state;
                                    if ($numericPrice > 0) {
                                        $set('sale_price', $numericPrice * 0.9); // Default 10% discount
                                    }
                                }
                            }),

                        Toggle::make('on_sale')
                            ->label('Diskon')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if (!$state) {
                                    $set('sale_price', null);
                                } else {
                                    $price = $get('price');
                                    // Clean and convert price to numeric
                                    if ($price) {
                                        $numericPrice = is_string($price) ? (float) str_replace([',', 'IDR', ' '], '', $price) : (float) $price;
                                        if ($numericPrice > 0 && !$get('sale_price')) {
                                            $set('sale_price', $numericPrice * 0.9); // Default 10% discount
                                        }
                                    }
                                }
                            }),

                        TextInput::make('sale_price')
                            ->label('Harga Diskon')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('IDR')
                            ->visible(fn (Get $get): bool => $get('on_sale'))
                            ->required(fn (Get $get): bool => $get('on_sale'))
                            ->rules([
                                fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    if ($get('on_sale') && $value) {
                                        $salePrice = is_string($value) ? (float) str_replace([',', 'IDR', ' '], '', $value) : (float) $value;
                                        $regularPrice = $get('price');
                                        $numericRegularPrice = is_string($regularPrice) ? (float) str_replace([',', 'IDR', ' '], '', $regularPrice) : (float) $regularPrice;

                                        if ($salePrice >= $numericRegularPrice) {
                                            $fail('Sale price must be lower than regular price.');
                                        }
                                    }
                                },
                            ]),
                    ]),

                    Section::make('Inventory')->schema([
                        TextInput::make('stock_quantity')
                            ->label('Stock Quantity')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                // Auto update in_stock based on stock_quantity
                                $set('in_stock', $state > 0);
                            }),

                        Toggle::make('in_stock')
                            ->label('In Stock')
                            ->default(true)
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                // If manually set to out of stock, set quantity to 0
                                if (!$state) {
                                    $set('stock_quantity', 0);
                                }
                            }),
                    ]),

                    Section::make('Associations')->schema([
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, $state) => $set('slug', Str::slug($state))),
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(),
                                Toggle::make('is_active')
                                    ->default(true),
                            ]),

                        Select::make('brand_id')
                            ->label('Brand')
                            ->relationship('brand', 'name')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, $state) => $set('slug', Str::slug($state))),
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(),
                                Toggle::make('is_active')
                                    ->default(true),
                            ]),
                    ]),

                    Section::make('Status')->schema([
                        Toggle::make('is_active')
                            ->label('Is Active')
                            ->default(true),

                        Toggle::make('is_featured')
                            ->label('Is Featured')
                            ->default(false),
                    ])
                ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->size(60)
                    ->circular()
                    ->getStateUsing(function (Product $record) {
                        // Get first image from array
                        $images = $record->image;
                        return is_array($images) && !empty($images) ? $images[0] : null;
                    }),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(30),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('brand.name')
                    ->label('Brand')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state == 0 => 'danger',
                        $state < 10 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn (string $state): string => $state . ' pcs'),

                TextColumn::make('price')
                    ->label('Price')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('sale_price')
                    ->label('Harga Diskon')
                    ->money('IDR')
                    ->sortable()
                    ->visible(fn ($record): bool => $record->on_sale ?? false)
                    ->color('danger')
                    ->weight('bold'),

                IconColumn::make('in_stock')
                    ->boolean()
                    ->label('In Stock')
                    ->tooltip(fn (Product $record): string => $record->in_stock ? 'Available' : 'Out of Stock'),

                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->tooltip(fn (Product $record): string => $record->is_active ? 'Active' : 'Inactive'),

                IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured')
                    ->tooltip(fn (Product $record): string => $record->is_featured ? 'Featured' : 'Not Featured'),

                IconColumn::make('on_sale')
                    ->boolean()
                    ->label('Sale')
                    ->tooltip(fn (Product $record): string => $record->on_sale ? 'On Sale' : 'Regular Price'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Category')
                    ->preload(),

                SelectFilter::make('brand_id')
                    ->relationship('brand', 'name')
                    ->label('Brand')
                    ->preload(),

                TernaryFilter::make('in_stock')
                    ->label('Stock Status')
                    ->placeholder('All products')
                    ->trueLabel('In Stock')
                    ->falseLabel('Out of Stock'),

                TernaryFilter::make('is_featured')
                    ->label('Featured')
                    ->placeholder('All products')
                    ->trueLabel('Featured only')
                    ->falseLabel('Not featured'),

                TernaryFilter::make('on_sale')
                    ->label('Sale Status')
                    ->placeholder('All products')
                    ->trueLabel('On Sale')
                    ->falseLabel('Regular Price'),

                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All products')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('updateStock')
                        ->label('Update Stock')
                        ->icon('heroicon-o-cube')
                        ->color('warning')
                        ->form([
                            TextInput::make('stock_quantity')
                                ->label('New Stock Quantity')
                                ->required()
                                ->numeric()
                                ->minValue(0)
                                ->default(fn (Product $record) => $record->stock_quantity),
                        ])
                        ->action(function (array $data, Product $record): void {
                            $record->update([
                                'stock_quantity' => $data['stock_quantity'],
                                'in_stock' => $data['stock_quantity'] > 0,
                            ]);
                        })
                        ->successNotificationTitle('Stock updated successfully'),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->tooltip('Actions'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('updateStockBulk')
                        ->label('Update Stock')
                        ->icon('heroicon-o-cube')
                        ->color('warning')
                        ->form([
                            TextInput::make('stock_quantity')
                                ->label('New Stock Quantity')
                                ->required()
                                ->numeric()
                                ->minValue(0)
                                ->helperText('This will update stock for all selected products'),
                        ])
                        ->action(function (array $data, $records): void {
                            foreach ($records as $record) {
                                $record->update([
                                    'stock_quantity' => $data['stock_quantity'],
                                    'in_stock' => $data['stock_quantity'] > 0,
                                ]);
                            }
                        })
                        ->successNotificationTitle('Stock updated for selected products'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s'); // Auto-refresh every 30 seconds
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $lowStockCount = static::getModel()::where('stock_quantity', '<=', 5)
            ->where('is_active', true)
            ->count();

        return $lowStockCount > 0 ? 'warning' : 'success';
    }
}
