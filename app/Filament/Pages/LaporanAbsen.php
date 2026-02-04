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
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Auth;
use App\Services\LaporanAbsenQuery;
use Filament\Actions\Action as TableAction;
use Filament\Notifications\Notification;
use App\Models\Absen;
use Filament\Forms\Components\TimePicker;

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
            ->headerActions([
                Action::make('print')
                    ->label('Cetak Laporan')
                    ->icon('heroicon-o-printer')
                    ->url(fn () => $this->printUrl())
                    ->openUrlInNewTab(),
            ])
            ->columns([
                TextColumn::make('tanggal')
                ->label('Tanggal')
                ->state(fn ($record) => $record->tanggal)
                ->date('d M Y'),
                TextColumn::make('absen_jam')
                ->label('Jam Absen')
                ->state(fn ($record) => $record->absen_jam ? substr($record->absen_jam, 0, 8) : '-')
                ->placeholder('-'),
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
            ])->defaultSort('tanggal')->emptyStateHeading('Tidak Ada Data')
            ->actions([
                TableAction::make('updateStatus')
                    ->label(fn ($record) => 'Ubah Status')
                    ->icon(fn ($record) => 'heroicon-o-pencil')
                    ->visible(fn () => auth()->user()?->role === 'guru')
                    ->modalHeading(fn ($record) => $record->absen_id
                        ? 'Ubah Status Absen'
                        : 'Buat Absen'
                    )
                    ->form([
                        Select::make('status')
                            ->label('Status Absen')
                            ->options([
                                'HADIR' => 'Hadir',
                                'TERLAMBAT' => 'Terlambat',
                                'PULANG' => 'Cabut / Keluar Pelajaran',
                                'TIDAK MASUK' => 'Tidak Masuk',
                            ])
                            ->default(fn ($record) => $record->absen_id
                                ? $record->status_absen
                                : null
                            )
                            ->required(),
                        TimePicker::make('jam')
                        ->default(fn ($record) => $record->absen_id
                                ? $record->absen_jam
                                : date('h:i:s')
                            )
                        ->required()
                    ])
                    ->action(function ($record, array $data) {
                        if (!$this->getGuruId()) {
                            Notification::make()
                                ->title('Tidak diizinkan')
                                ->danger()
                                ->send();
                            return;
                        }

                        Absen::updateOrCreate(
                            [
                                'tanggal' => $record->tanggal,
                                'siswa_id' => $record->siswa_id,
                                'jadwal_pelajaran_id' => $record->jadwal_pelajaran_id,
                            ],
                            [
                                'status' => $data['status'],
                                'jam' => $data['jam']
                            ]
                        );

                        Notification::make()
                            ->title('Status absen berhasil diperbarui')
                            ->success()
                            ->send();

                        $this->resetTable();
                    }),
            ]);
    }

    protected function baseQuery(): EloquentBuilder
    {
        return LaporanAbsenRow::query()
        ->fromSub(
            LaporanAbsenQuery::build(
                $this->getTableFilterState('filter') ?? [],
                $this->getGuruId(),
                $this->getOrangTuaSiswaIds()
            ),
            'laporan_absen_rows'
        )
        ->select('*')
        ->addSelect('row_id');
    }

    protected function reportSubQuery(): QueryBuilder
    {
        $filter = $this->getTableFilterState('filter') ?? [];

        $from   = $filter['from'] ?? now()->toDateString();
        $until  = $filter['until'] ?? now()->toDateString();
        $kelasId = $filter['kelas_id'] ?? null;
        $mapelId = $filter['mata_pelajaran_id'] ?? null;
        $guruId  = $this->getGuruId();

        return DB::table('calendar_dates')

            ->whereBetween('calendar_dates.tanggal', [$from, $until])

            // 1️⃣ Jadwal berdasarkan hari
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

            // 2️⃣ Siswa sesuai kelas jadwal
            ->join('siswas', 'siswas.kelas_id', '=', 'jadwal_pelajarans.kelas_id')
            ->join('kelas', 'kelas.id', '=', 'siswas.kelas_id')
            ->join('mata_pelajarans', 'mata_pelajarans.id', '=', 'jadwal_pelajarans.mata_pelajaran_id')

            // 3️⃣ Absens (LEFT JOIN)
            ->leftJoin('absens', function ($join) {
                $join->on('absens.tanggal', '=', 'calendar_dates.tanggal')
                    ->on('absens.siswa_id', '=', 'siswas.id')
                    ->on('absens.jadwal_pelajaran_id', '=', 'jadwal_pelajarans.id');
            })

            // 4️⃣ Filter opsional
            ->when($guruId, fn ($q) =>
                $q->where('jadwal_pelajarans.guru_id', $guruId)
            )
            ->when($kelasId, fn ($q) =>
                $q->where('jadwal_pelajarans.kelas_id', $kelasId)
            )
            ->when($mapelId, fn ($q) =>
                $q->where('jadwal_pelajarans.mata_pelajaran_id', $mapelId)
            )

            // 5️⃣ SELECT FINAL
            ->select([
                DB::raw("
                    CONCAT(
                        calendar_dates.tanggal, '-',
                        siswas.id, '-',
                        jadwal_pelajarans.id
                    ) AS row_id
                "),
                'calendar_dates.tanggal',
                'siswas.id AS siswa_id',
                'jadwal_pelajarans.id AS jadwal_pelajaran_id',
                'absens.id AS absen_id',
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


    protected function getGuruId(): ?int
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'guru') {
            return null;
        }

        return $user->guru?->id;
    }

    protected function printUrl(): string
    {
        $filter = $this->getTableFilterState('filter') ?? [];

        return route('laporan.absen.print', [
            'from' => $filter['from'] ?? null,
            'until' => $filter['until'] ?? null,
            'kelas_id' => $filter['kelas_id'] ?? null,
            'mata_pelajaran_id' => $filter['mata_pelajaran_id'] ?? null,
        ]);
    }

    protected function getOrangTuaSiswaIds(): ?array
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'orang tua') {
            return null;
        }

        return $user->orangTua
            ?->siswas()
            ->pluck('siswas.id')
            ->toArray();
    }

}
