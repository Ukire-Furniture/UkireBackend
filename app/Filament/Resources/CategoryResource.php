<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    // Model terkait untuk resource ini
    protected static ?string $model = Category::class;

    // Ikon untuk navigasi di sidebar Filament
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    // Label navigasi di sidebar Filament
    protected static ?string $navigationLabel = 'Kategori Produk';
    // Grup navigasi
    protected static ?string $navigationGroup = 'Manajemen Toko';

    /**
     * Mendefinisikan form untuk membuat/mengedit kategori.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Input teks untuk nama kategori
                Forms\Components\TextInput::make('name')
                    ->label('Nama Kategori') // Label di UI
                    ->required() // Wajib diisi
                    ->maxLength(255) // Batasan panjang karakter
                    ->unique(ignoreRecord: true), // Harus unik, abaikan rekaman saat edit
            ]);
    }

    /**
     * Mendefinisikan tabel untuk menampilkan daftar kategori.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom teks untuk nama kategori
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable() // Bisa dicari
                    ->sortable(), // Bisa diurutkan
                // Kolom teks untuk tanggal dibuat
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime() // Format sebagai tanggal dan waktu
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Bisa disembunyikan secara default
                // Kolom teks untuk tanggal diupdate
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Tanggal Diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(), // Aksi edit
                Tables\Actions\DeleteAction::make(), // Aksi hapus
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(), // Aksi hapus massal
                ]),
            ]);
    }

    /**
     * Mendapatkan halaman-halaman terkait untuk resource ini.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'), // Halaman daftar
            'create' => Pages\CreateCategory::route('/create'), // Halaman buat baru
            'edit' => Pages\EditCategory::route('/{record}/edit'), // Halaman edit
        ];
    }
}

