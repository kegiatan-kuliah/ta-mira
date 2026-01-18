<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Siswa;

class DashboardWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Operator', User::where('role', 'operator')->count()),
            Stat::make('Total Guru', User::where('role', 'guru')->count()),
            Stat::make('Total Siswa', Siswa::count()),
        ];
    }
}
