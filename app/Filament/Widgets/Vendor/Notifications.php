<?php

namespace App\Filament\Widgets\Vendor;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use Filament\Facades\Filament;
use Faker\Factory as Faker;
use Illuminate\View\View;

class Notifications extends Widget
{
    protected static string $view = 'filament.widgets.vendor.notifications';
    protected static ?int $sort = 6; // Position in dashboard
    protected static bool $isLazy = false; // Load instantly

}
