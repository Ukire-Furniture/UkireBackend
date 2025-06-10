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
// use Filament\Forms\Components\TextInput\Mask; // Baris ini tidak lagi diperlukan jika mask dihilangkan

class ProductResource extends Resource
{
    // Model terkait untuk resource ini
    protected static ?string $model = Product::class;

    // Ikon untuk navigasi di sidebar Filament
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    // Label navigasi di sidebar Filament
    protected static ?string $navigationLabel = 'Produk Ukiran';
    // Grup navigasi
    protected static ?string $navigationGroup = 'Manajemen Toko';

    /**
     * Mendefinisikan form untuk membuat/mengedit produk.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Produk')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Produk')
                            ->rows(5)
                            ->nullable(),

                        Forms\Components\TextInput::make('price')
                            ->label('Harga')
                            ->required()
                            ->numeric() // Tetap memastikan input hanya angka
                            ->prefix('IDR'), // Tetap menampilkan prefiks mata uang
                            // Bagian mask dihilangkan untuk mengatasi error yang terus-menerus
                            // ->mask(function (Mask $mask) {
                            //     return $mask
                            //         ->numeric()
                            //         ->thousandsSeparator(',')
                            //         ->decimalSeparator('.')
                            //         ->mapToDecimal();
                            // }),

                        Forms\Components\TextInput::make('stock')
                            ->label('Stok')
                            ->required()
                            ->numeric()
                            ->default(0),

                        Forms\Components\FileUpload::make('image_path')
                            ->label('Gambar Produk')
                            ->image()
                            ->directory('product-images')
                            ->maxSize(2048)
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    /**
     * Mendefinisikan tabel untuk menampilkan daftar produk.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Gambar')
                    ->square(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Tanggal Diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label('Filter Kategori'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Mendapatkan halaman-halaman terkait untuk resource ini.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
