<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\TextColumn;
use App\Models\LaporanAbsenRow;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Auth;

class LaporanAbsen extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.laporan-absen';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::PresentationChartLine;
    protected static ?string $navigationLabel = 'Laporan Absen';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->baseQuery())
            ->columns([
                TextColumn::make('tanggal')
                ->label('Tanggal')
                ->state(fn ($record) => $record->tanggal)
                ->date('d M Y'),
                TextColumn::make('nis')->label('NIS'),
                TextColumn::make('nama')->label('Nama Siswa'),
                TextColumn::make('kelas')->label('Kelas'),
                TextColumn::make('mata_pelajaran')->label('Mata Pelajaran'),
                TextColumn::make('hari')->label('Hari'),
                TextColumn::make('jam')
                    ->label('Jam')
                    ->state(fn ($record) =>
                        substr($record->jam_mulai, 0, 5)
                        . ' - ' .
                        substr($record->jam_selesai, 0, 5)
                    ),

                TextColumn::make('status_absen')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'HADIR' => 'success',
                        'TERLAMBAT' => 'warning',
                        'TIDAK MASUK' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Filter::make('filter')
                    ->form([
                        DatePicker::make('from')
                            ->label('Dari Tanggal')
                            ->required(),

                        DatePicker::make('until')
                            ->label('Sampai Tanggal')
                            ->required(),

                        Select::make('kelas_id')
                            ->label('Kelas')
                            ->options(function () {
                                $guruId = $this->getGuruId();

                                if ($guruId) {
                                    return Kelas::query()
                                        ->whereIn('id', function ($q) use ($guruId) {
                                            $q->select('kelas_id')
                                                ->from('jadwal_pelajarans')
                                                ->where('guru_id', $guruId);
                                        })
                                        ->pluck('nama', 'id');
                                }

                                return Kelas::pluck('nama', 'id');
                            })
                            ->searchable(),

                        Select::make('mata_pelajaran_id')
                            ->label('Mata Pelajaran')
                            ->options(function () {
                                $guruId = $this->getGuruId();

                                if ($guruId) {
                                    return MataPelajaran::query()
                                        ->whereIn('id', function ($q) use ($guruId) {
                                            $q->select('mata_pelajaran_id')
                                                ->from('jadwal_pelajarans')
                                                ->where('guru_id', $guruId);
                                        })
                                        ->pluck('nama', 'id');
                                }

                                return MataPelajaran::pluck('nama', 'id');
                            })
                            ->searchable(),
                    ]),
            ])->defaultSort('tanggal')->emptyStateHeading('Tidak Ada Data');
    }

    protected function baseQuery(): EloquentBuilder
    {
        return LaporanAbsenRow::query()
            ->fromSub(
                $this->reportSubQuery(),
                'laporan_absen_rows'
            );
    }

    protected function reportSubQuery(): QueryBuilder
    {
        $filter = $this->getTableFilterState('filter') ?? [];

        $from = $filter['from'] ?? now()->toDateString();
        $until = $filter['until'] ?? now()->toDateString();
        $kelasId = $filter['kelas_id'] ?? null;
        $mapelId = $filter['mata_pelajaran_id'] ?? null;
        $guruId = $this->getGuruId();

        return DB::table('calendar_dates')
        ->whereBetween('calendar_dates.tanggal', [$from, $until])

        ->crossJoin('siswas')
        ->join('kelas', 'kelas.id', '=', 'siswas.kelas_id')

        // ⬇️ JOIN JADWAL BERDASARKAN HARI
        ->join('jadwal_pelajarans', function ($join) {
            $join->whereRaw("
                UPPER(jadwal_pelajarans.hari) =
                CASE DAYOFWEEK(calendar_dates.tanggal)
                    WHEN 2 THEN 'MONDAY'
                    WHEN 3 THEN 'TUESDAY'
                    WHEN 4 THEN 'WEDNESDAY'
                    WHEN 5 THEN 'THURSDAY'
                    WHEN 6 THEN 'FRIDAY'
                    WHEN 7 THEN 'SATURDAY'
                    ELSE 'MINGGU'
                END
            ");
        })

        ->join('mata_pelajarans', 'mata_pelajarans.id', '=', 'jadwal_pelajarans.mata_pelajaran_id')

        ->leftJoin('absens', function ($join) {
            $join->on('absens.tanggal', '=', 'calendar_dates.tanggal')
                ->on('absens.siswa_id', '=', 'siswas.id')
                ->on('absens.jadwal_pelajaran_id', '=', 'jadwal_pelajarans.id');
        })

        ->when($guruId, fn ($q) =>
            $q->where('jadwal_pelajarans.guru_id', $guruId)
        )

        ->when($kelasId, fn ($q) =>
            $q->where('siswas.kelas_id', $kelasId)
        )
        ->when($mapelId, fn ($q) =>
            $q->where('jadwal_pelajarans.mata_pelajaran_id', $mapelId)
        )

        ->select([
            DB::raw("
                CONCAT(
                    calendar_dates.tanggal, '-', 
                    siswas.id, '-', 
                    jadwal_pelajarans.id
                ) as row_id
            "),
            DB::raw('calendar_dates.tanggal as tanggal'),
            'siswas.id as siswa_id',
            'siswas.nis',
            'siswas.nama',
            'kelas.nama as kelas',
            'mata_pelajarans.nama as mata_pelajaran',
            DB::raw("
                CASE LOWER(jadwal_pelajarans.hari)
                    WHEN 'monday' THEN 'Senin'
                    WHEN 'tuesday' THEN 'Selasa'
                    WHEN 'wednesday' THEN 'Rabu'
                    WHEN 'thursday' THEN 'Kamis'
                    WHEN 'friday' THEN 'Jumat'
                    WHEN 'saturday' THEN 'Sabtu'
                    ELSE 'Minggu'
                END as hari
            "),
            'jadwal_pelajarans.jam_mulai',
            'jadwal_pelajarans.jam_selesai',
            DB::raw("
                CASE
                    WHEN absens.id IS NULL THEN 'TIDAK MASUK'
                    ELSE absens.status
                END as status_absen
            "),
        ]);
    }

    protected function getGuruId(): ?int
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'guru') {
            return null;
        }

        return $user->guru?->id;
    }

}
