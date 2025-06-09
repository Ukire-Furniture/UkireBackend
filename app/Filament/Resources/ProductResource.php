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
// BARIS PENTING INI: Pastikan ini ada dan tidak ada typo
use Filament\Forms\Components\TextInput\Mask;

class ProductResource extends Resource
{
    // Model terkait untuk resource ini
    protected static ?string $model = Product::class;

    // Ikon untuk navigasi di sidebar Filament
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    // Label navigasi di sidebar Filament
    protected static ?string $navigationLabel = 'Produk Ukire';
    // Grup navigasi
    protected static ?string $navigationGroup = 'Manajemen Toko';

    /**
     * Mendefinisikan form untuk membuat/mengedit produk.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make() // Menggunakan Card untuk pengelompokan
                    ->schema([
                        // Input teks untuk nama produk
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Produk')
                            ->required()
                            ->maxLength(255),

                        // Select box untuk memilih kategori
                        Forms\Components\Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name') // Relasi ke model Category, menampilkan kolom 'name'
                            ->required()
                            ->searchable() // Bisa dicari
                            ->preload(), // Memuat semua kategori di awal

                        // Textarea untuk deskripsi produk
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Produk')
                            ->rows(5) // Tinggi textarea
                            ->nullable(),

                        // Input numerik untuk harga
                        Forms\Components\TextInput::make('price')
                            ->label('Harga')
                            ->required()
                            ->numeric() // Hanya menerima angka
                            ->prefix('IDR') // Prefiks mata uang
                            // Bagian mask yang sudah dikoreksi
                            ->mask(function (Mask $mask) {
                                return $mask
                                    ->numeric()
                                    ->thousandsSeparator(',')
                                    ->decimalSeparator('.')
                                    ->mapToDecimal();
                            }), // Format angka dengan pemisah ribuan

                        // Input numerik untuk stok
                        Forms\Components\TextInput::make('stock')
                            ->label('Stok')
                            ->required()
                            ->numeric()
                            ->default(0), // Nilai default 0

                        // Upload file untuk gambar produk
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Gambar Produk')
                            ->image() // Hanya menerima gambar
                            ->directory('product-images') // Direktori penyimpanan di storage/app/public
                            ->maxSize(2048) // Ukuran maksimal file dalam KB (contoh: 2MB)
                            ->nullable(),
                    ])->columns(2), // 2 kolom dalam card
            ]);
    }

    /**
     * Mendefinisikan tabel untuk menampilkan daftar produk.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom gambar
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Gambar')
                    ->square(), // Mengatur gambar menjadi kotak

                // Kolom teks untuk nama produk
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable(),

                // Kolom teks untuk kategori produk (menggunakan relasi)
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),

                // Kolom teks untuk harga produk
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR') // Format sebagai mata uang IDR
                    ->sortable(),

                // Kolom teks untuk stok produk
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->numeric() // Format sebagai angka
                    ->sortable(),

                // Kolom teks untuk tanggal dibuat
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Kolom teks untuk tanggal diupdate
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Tanggal Diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter berdasarkan kategori
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
