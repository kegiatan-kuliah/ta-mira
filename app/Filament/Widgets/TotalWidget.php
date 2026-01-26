<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use App\Services\LaporanAbsenQuery;
use Carbon\Carbon;

class TotalWidget extends ChartWidget
{
    protected ?string $heading = 'Total Absen Dalam Bulan Ini';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $from = now()->startOfMonth()->toDateString();
        $until = now()->endOfMonth()->toDateString();

        $guruId = auth()->user()?->role === 'guru'
            ? auth()->user()->guru?->id
            : null;

        /**
         * Pakai QUERY YANG SAMA dengan laporan
         */
        $rows = DB::query()
            ->fromSub(
                LaporanAbsenQuery::build(
                    [
                        'from' => $from,
                        'until' => $until,
                    ],
                    $guruId
                ),
                'laporan'
            )
            ->select(
                DB::raw("
                    CASE
                        WHEN status_absen IS NULL THEN 'TIDAK MASUK'
                        ELSE status_absen
                    END AS status
                "),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Absen Bulan Ini',
                    'data' => [
                        $rows['HADIR'] ?? 0,
                        $rows['TERLAMBAT'] ?? 0,
                        $rows['TIDAK MASUK'] ?? 0,
                    ],
                    'backgroundColor' => [
                        '#22c55e',
                        '#facc15',
                        '#ef4444',
                    ],
                ],
            ],
            'labels' => [
                'Hadir',
                'Terlambat',
                'Tidak Masuk',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
