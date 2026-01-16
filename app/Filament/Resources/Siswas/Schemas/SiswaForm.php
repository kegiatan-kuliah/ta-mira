<?php

namespace App\Filament\Resources\Siswas\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\Kelas;

class SiswaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nis')
                    ->label('NIS')
                    ->required(),
                TextInput::make('nama')
                    ->required(),
                Select::make('kelas_id')
                    ->label('Pilih Kelas')
                    ->required()
                    ->options(Kelas::query()->pluck('nama', 'id'))
                    ->searchable(),
                Select::make('status')
                    ->label('Pilih Status')
                    ->required()
                    ->options([
                        'AKTIF' => 'AKTIF',
                        'LULUS' => 'LULUS'
                    ]),
            ]);
    }
}
