<?php

namespace App\Filament\Widgets\Vendor;

use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Facades\Filament;
use App\Filament\Resources\ProductResource;
use Filament\Tables\Columns\TextColumn;


class RecentOrders extends BaseWidget
{
    protected static ?int $sort = 4; 
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        
        return $table
        ->query(ProductResource::getEloquentQuery())
        ->defaultPaginationPageOption(5)
        ->defaultSort('created_at', 'desc')
        ->columns([
            TextColumn::make('number')
                ->searchable()
                ->sortable(),

            TextColumn::make('customer.name')
                ->searchable()
                ->sortable()
                ->toggleable(),

            TextColumn::make('status')
                ->searchable()
                ->sortable(),

            TextColumn::make('created_at')
                ->label('Order Date')
                ->date(),
        ]);   
    }

    
    public static function canView(): bool
    {
        return Filament::auth()->user()->hasRole('Vendor'); // 👈 Ensure only vendors see this widget
    }

}
