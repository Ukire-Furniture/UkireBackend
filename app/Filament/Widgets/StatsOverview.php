<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;

class StatsOverview extends BaseWidget
{
    protected static bool $is_enabled = true; // Pastikan ini true agar widget muncul

    protected function getStats(): array
    {
        // Ambil data statistik dari database
        $totalProducts = Product::count();
        $totalUsers = User::count();
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalRevenue = Order::where('status', 'completed')->sum('total_amount');

        return [
            Stat::make('Total Produk', $totalProducts)
                ->description('Jumlah produk ukiran di toko')
                ->descriptionIcon('heroicon-s-cube')
                ->color('success'),
            Stat::make('Total Pengguna', $totalUsers)
                ->description('Jumlah akun pengguna terdaftar')
                ->descriptionIcon('heroicon-s-users')
                ->color('info'),
            Stat::make('Total Pesanan', $totalOrders)
                ->description('Jumlah keseluruhan pesanan')
                ->descriptionIcon('heroicon-s-shopping-bag')
                ->color('primary'),
            Stat::make('Pesanan Pending', $pendingOrders)
                ->description('Pesanan menunggu diproses')
                ->descriptionIcon('heroicon-s-clock')
                ->color('warning'),
            Stat::make('Total Pendapatan', 'IDR ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Pendapatan dari pesanan selesai')
                ->descriptionIcon('heroicon-s-currency-dollar')
                ->color('success'),
        ];
    }
}
