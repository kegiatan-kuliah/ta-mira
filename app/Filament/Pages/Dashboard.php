<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\DashboardWidget;
use App\Filament\Widgets\LastAbsenWidget;

class Dashboard extends BaseDashboard
{
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