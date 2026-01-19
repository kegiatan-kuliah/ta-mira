<?php

namespace App\Filament\Resources\JadwalPelajarans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Models\Guru;
use App\Models\MataPelajaran;
use App\Models\Kelas;

class JadwalPelajaranForm
{
    public static function configure(Schema $schema): Schema
    {
        
        return $schema
            ->columns(1)
            ->components([
                Select::make('hari')
                    ->label('Pilih Hari')
                    ->required()
                    ->options([
                        'monday' => 'Senin',
                        'tuesday' => 'Selasa',
                        'wednesday' => 'Rabu',
                        'thursday' => 'Kamis',
                        'friday' => 'Jumat',
                        'saturday' => 'Sabtu',
                    ]),
                TimePicker::make('jam_mulai')
                    ->required(),
                TimePicker::make('jam_selesai')
                    ->required(),
                Select::make('kelas_id')
                    ->label('Pilih Kelas')
                    ->required()
                    ->options(Kelas::query()->pluck('nama', 'id'))
                    ->searchable(),
                Select::make('guru_id')
                    ->label('Pilih Guru')
                    ->required()
                    ->options(Guru::query()->pluck('nama', 'id'))
                    ->searchable(),
                Select::make('mata_pelajaran_id')
                    ->label('Pilih Mata Pelajaran')
                    ->required()
                    ->options(MataPelajaran::query()->pluck('nama', 'id'))
                    ->searchable(),
            ]);
    }
}
