<?php

namespace App\Filament\Widgets\Vendor;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Filament\Resources\ProductResource;
use Filament\Facades\Filament;
use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\Product;

class StatsWidget extends BaseWidget
{
    protected static ?int $chartHeight = 600;
    protected function getStats(): array
    {
        $user = Filament::auth()->user(); // Get the logged-in user
        return [
            Stat::make('Total Products Listed', Product::where('created_by', $user->id)->count())
                ->description('Added Products')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->url(ProductResource::getUrl()) // 👈 Clickable link to ProductResource
                ->openUrlInNewTab(false), // Optional: Open in the same tab
            Stat::make('Pending Orders', Order::where('status', OrderStatusEnum::Draft->value)->count())
                ->description('Total products in app')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),
            Stat::make('Revenue', Product::count())
                ->description('Total Revenue this month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),
        ];
    }

    // Only show this widget for vendors
    public static function canView(): bool
    {
        return Filament::auth()->user()->hasRole('Vendor'); // 👈 Ensure only vendors see this widget
    }
}
