<?php

namespace App\Filament\Widgets\Vendor;

use Filament\Widgets\ChartWidget;
use App\Models\Order;
use Filament\Facades\Filament;

class OrderStatus extends ChartWidget
{
    protected static ?string $heading = 'Order Status';
    protected static ?int $sort = 3; 

    protected function getData(): array
    {
        // Count orders for each status
        $pendingCount = Order::where('status', 'draft')->count();
        $completedCount = Order::where('status', 'paid')->count();
        $canceledCount = Order::where('status', 'cancelled')->count();

        return [
            'datasets' => [
                [
                    'data' => [$pendingCount, $completedCount, $canceledCount], // Order Counts
                    'backgroundColor' => ['#FACC15', '#22C55E', '#E11D48'], // Colors for each status
                ],
            ],
            'labels' => ['Draft', 'Paid', 'Cancelled'], // Labels
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public static function canView(): bool
    {
        return Filament::auth()->user()->hasRole('Vendor'); // 👈 Ensure only vendors see this widget
    }
}
