<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use App\Models;

class Stats extends BaseWidget
{
    use HasWidgetShield;
    
    protected function getStats(): array
    {
        $users = Models\User::query()->count();
        $adusers = Models\AdUsers::query()->count();
        $adgroups = Models\AdGroups::query()->count();
        $adous = Models\AdOrganizationalUnits::query()->count();
        $computers = Models\Client::query()->count();

        return [
            Stat::make('Number Of Local Users', $users),
            Stat::make('Number Of AD groups', $adgroups),
            Stat::make('Number Of AD users', $adusers),
            Stat::make('Number Of AD Organizational Units', $adous),
            Stat::make('Number Of AD Computers', $adous),
        ];
    }
}
