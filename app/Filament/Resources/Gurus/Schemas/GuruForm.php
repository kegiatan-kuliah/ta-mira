<?php

namespace App\Filament\Resources\Gurus\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GuruForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nip')
                    ->required(),
                TextInput::make('nama')
                    ->required(),
                TextInput::make('email')
                    ->required()
                    ->email(),
                TextInput::make('password')
                    ->password()
                    ->required(),
            ]);
    }
}
