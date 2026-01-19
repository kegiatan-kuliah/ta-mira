<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\DashboardWidget;
use App\Filament\Widgets\LastAbsenWidget;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Home;

    public function getHeading(): string
    {
        return 'Selamat Datang';
    }

    public function getSubheading(): ?string
    {
        return 'Sistem Informasi Absen Siswa SMK CITRA UTAMA PADANG';
    }

    public function getWidgets(): array
    {
        return [
            DashboardWidget::class,
            LastAbsenWidget::class
        ];
    }
}