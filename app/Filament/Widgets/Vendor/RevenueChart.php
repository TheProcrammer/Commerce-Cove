<?php

namespace App\Filament\Widgets\Vendor;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Filament\Facades\Filament;
use App\Models\Order;
use Filament\Actions\Action;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Revenue Trend';

    public ?string $chartType = 'line'; // Default chart type

    public ?int $selectedYear = null; // Stores the selected year
    protected static ?int $sort = 3; 

    protected function getData(): array
    {
        $user = Filament::auth()->user();

        // Default to current year if no year is selected
        $year = $this->selectedYear ?? now()->year;

        // Get revenue data for each month
        $revenue = Order::where('vendor_id', $user->id) // Filter by vendor
            ->whereYear('created_at', $year)
            ->select(
                DB::raw('strftime("%m", created_at) as month'),
                DB::raw('SUM(vendor_subtotal) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Fill missing months with 0
        $monthlyRevenue = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyRevenue[] = $revenue[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => "Revenue ($year)",
                    'data' => $monthlyRevenue,
                    'borderColor' => '#4F46E5',
                    'backgroundColor' => 'rgba(79, 70, 229, 0.5)',
                ],
            ],
            'labels' => [
                'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
            ],      
        ];
    }

    protected function getExtraHeaderActions(): array
    {
        return [
            Action::make('line')
                ->label('Line')
                ->action(fn () => $this->updateChartType('line'))
                ->color('primary'),

            Action::make('bar')
                ->label('Bar')
                ->action(fn () => $this->updateChartType('bar'))
                ->color('secondary'),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public static function canView(): bool
    {
        return Filament::auth()->user()->hasRole('Vendor');
    }

}
