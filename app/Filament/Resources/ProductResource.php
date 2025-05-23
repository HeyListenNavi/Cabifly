<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Tables\Columns\TextColumn;


class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->required()
                ->columnSpanFull()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(function (?string $state, Forms\Set $set) {
                    $set('slug', Str::slug($state));
                }),
            Forms\Components\Textarea::make('description')
                ->columnSpanFull(),

            Forms\Components\TextInput::make('price')
                ->required()
                ->numeric()
                ->prefix('$'),

            Forms\Components\TextInput::make('stock_quantity')
                ->required()
                ->numeric()
                ->minValue(0),

            Forms\Components\Select::make('category')
                ->required()
                ->options([
                    'tecnologia' => 'Tecnología',
                    'moda' => 'Moda',
                    'hogar' => 'Hogar',
                    'juguetes' => 'Juguetes',
                    'otros' => 'Otros',
                ])
                ->label('Categoría')
                ->searchable(), // Opcional: permite buscar dentro del select


            Forms\Components\FileUpload::make('image')
                ->image()
                ->disk('public')
                ->directory('products')
                ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                ->url(fn ($record) => asset('storage/' . $record->image))
                ->disk('public'),

            Tables\Columns\TextColumn::make('name')
                ->searchable(),

            Tables\Columns\TextColumn::make('price')
                ->money('USD')
                ->sortable(),

            Tables\Columns\TextColumn::make('stock_quantity')
                ->numeric()
                ->sortable(),
            ])
            ->filters([
                //
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
}
