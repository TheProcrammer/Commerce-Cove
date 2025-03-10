<?php

namespace App\Filament\Widgets\Vendor;

use Filament\Widgets\Widget;

class Reviews extends Widget
{
    protected static string $view = 'filament.widgets.vendor.reviews';
    protected static ?int $sort = 6; // Position in dashboard
    protected static bool $isLazy = false; // Load instantly

    protected int | string | array $columnSpan = 1; 
}
