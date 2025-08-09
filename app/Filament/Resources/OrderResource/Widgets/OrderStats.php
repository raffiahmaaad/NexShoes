<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use App\Models\OrderItem;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class OrderStats extends BaseWidget
{
    /**
     * Membuat seluruh widget diperbarui otomatis.
     * Mengubah ke 1 detik bisa memberatkan server, 5-15 detik biasanya cukup.
     */
    protected static ?string $pollingInterval = '5s';

    /**
     * Fungsi asli Anda untuk memformat angka.
     */
    private function formatCompactNumber(int|float|null $number): string
    {
        if (is_null($number) || $number == 0) {
            return '0';
        }

        if ($number < 1000) {
            return (string) $number;
        }

        if ($number < 1000000) {
            return round($number / 1000, 1) . 'K';
        }

        return round($number / 1000000, 1) . 'JT';
    }

    protected function getStats(): array
    {
        // --- Mengambil data untuk semua grafik 7 hari terakhir ---

        $totalOrdersData = Order::query()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->where('created_at', '>=', now()->subDays(7))
            ->pluck('count')
            ->toArray();

        $processingData = Order::query()
            ->where('status', 'processing')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->where('created_at', '>=', now()->subDays(7))
            ->pluck('count')
            ->toArray();

        $shippedData = Order::query()
            ->where('status', 'shipped')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->where('created_at', '>=', now()->subDays(7))
            ->pluck('count')
            ->toArray();

        // --- PERBAIKAN: Grafik pendapatan diambil dari total item DAN difilter 'paid' ---
        $totalRevenueData = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid') // Filter 'paid' ditambahkan
            ->select(DB::raw('DATE(orders.created_at) as date'), DB::raw('sum(order_items.total_amount) as revenue'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->where('orders.created_at', '>=', now()->subDays(7))
            ->pluck('revenue')
            ->toArray();


        return [
            // --- PERBAIKAN: Statistik pendapatan diambil dari total item DAN difilter 'paid' ---
            Stat::make('Total Revenue', 'IDR ' . $this->formatCompactNumber(
                OrderItem::query()
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->where('orders.payment_status', 'paid') // Filter 'paid' ditambahkan
                    ->sum('order_items.total_amount')
            ))
                ->description('Total pendapatan dari pesanan lunas')
                ->descriptionIcon('heroicon-m-chart-bar-square')
                ->color('success')
                ->chart($totalRevenueData),

            Stat::make('Orders', Order::count())
                ->description('Jumlah semua pesanan')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary')
                ->chart($totalOrdersData),

            Stat::make('Order Processing', Order::query()->where('status', 'processing')->count())
                ->description('Pesanan sedang diproses')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning')
                ->chart($processingData),

            Stat::make('Order Shipped', Order::query()->where('status', 'shipped')->count())
                ->description('Pesanan telah dikirim')
                ->descriptionIcon('heroicon-m-truck')
                ->color('success')
                ->chart($shippedData),
        ];
    }
}
