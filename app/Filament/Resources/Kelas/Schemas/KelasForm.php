<?php

namespace App\Filament\Resources\Kelas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use App\Models\Jurusan;

class KelasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('nama')
                    ->required(),
                Select::make('jurusan_id')
                    ->label('Pilih Jurusan')
                    ->required()
                    ->options(Jurusan::query()->pluck('nama', 'id'))
                    ->searchable(),
            ]);
    }
}
