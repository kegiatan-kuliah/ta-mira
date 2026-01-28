<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class LaporanAbsenQuery
{
    public static function build(array $filter = [], ?int $guruId = null, ?array $siswaIds = null): Builder
    {
        $from    = $filter['from'] ?? now()->toDateString();
        $until   = $filter['until'] ?? now()->toDateString();
        $kelasId = $filter['kelas_id'] ?? null;
        $mapelId = $filter['mata_pelajaran_id'] ?? null;

        return DB::table('calendar_dates')
            ->whereBetween('calendar_dates.tanggal', [$from, $until])

            // Jadwal sesuai hari
            ->join('jadwal_pelajarans', function ($join) {
                $join->whereRaw("
                    LOWER(jadwal_pelajarans.hari) =
                    CASE DAYOFWEEK(calendar_dates.tanggal)
                        WHEN 2 THEN 'monday'
                        WHEN 3 THEN 'tuesday'
                        WHEN 4 THEN 'wednesday'
                        WHEN 5 THEN 'thursday'
                        WHEN 6 THEN 'friday'
                        WHEN 7 THEN 'saturday'
                        ELSE 'minggu'
                    END
                ");
            })

            // Siswa sesuai kelas jadwal
            ->join('siswas', 'siswas.kelas_id', '=', 'jadwal_pelajarans.kelas_id')
            ->join('kelas', 'kelas.id', '=', 'siswas.kelas_id')
            ->join('mata_pelajarans', 'mata_pelajarans.id', '=', 'jadwal_pelajarans.mata_pelajaran_id')

            // Absens
            ->leftJoin('absens', function ($join) {
                $join->on('absens.tanggal', '=', 'calendar_dates.tanggal')
                    ->on('absens.siswa_id', '=', 'siswas.id')
                    ->on('absens.jadwal_pelajaran_id', '=', 'jadwal_pelajarans.id');
            })

            ->when($guruId, fn ($q) =>
                $q->where('jadwal_pelajarans.guru_id', $guruId)
            )
            ->when($kelasId, fn ($q) =>
                $q->where('jadwal_pelajarans.kelas_id', $kelasId)
            )
            ->when($mapelId, fn ($q) =>
                $q->where('jadwal_pelajarans.mata_pelajaran_id', $mapelId)
            )
            ->when(
                is_array($siswaIds) && count($siswaIds) > 0,
                fn ($q) => $q->whereIn('siswas.id', $siswaIds)
            )

            ->select([
                DB::raw("
                    CONCAT(
                        calendar_dates.tanggal, '-',
                        siswas.id, '-',
                        jadwal_pelajarans.id
                    ) AS row_id
                "),
                'calendar_dates.tanggal',
                'siswas.nis',
                'siswas.nama',
                'kelas.nama AS kelas',
                'mata_pelajarans.nama AS mata_pelajaran',
                'jadwal_pelajarans.jam_mulai',
                'jadwal_pelajarans.jam_selesai',
                'absens.jam AS absen_jam',
                DB::raw("
                    CASE
                        WHEN absens.id IS NULL THEN 'TIDAK MASUK'
                        ELSE absens.status
                    END AS status_absen
                "),
            ]);
    }
}
