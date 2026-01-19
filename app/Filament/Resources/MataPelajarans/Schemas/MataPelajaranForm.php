<?php

namespace App\Filament\Resources\MataPelajarans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MataPelajaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('nama')
                    ->required(),
            ]);
    }
}
