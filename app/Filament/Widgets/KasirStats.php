<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KasirStats extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->hasRole('kasir');
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Today Sales', '$450.00')
                ->description('Pending processing')
                ->icon('heroicon-o-currency-dollar'),
            Stat::make('Open Registers', '2')
                ->icon('heroicon-o-computer-desktop'),
            Stat::make('Transactions', '15')
                ->icon('heroicon-o-shopping-cart'),
        ];
    }
}
