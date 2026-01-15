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
            ->components([
                TextInput::make('nama')
                    ->required(),
                Select::make('jurusan_id')
                    ->label('Pilih Jurusan')
                    ->options(Jurusan::query()->pluck('nama', 'id'))
                    ->searchable(),
            ]);
    }
}
