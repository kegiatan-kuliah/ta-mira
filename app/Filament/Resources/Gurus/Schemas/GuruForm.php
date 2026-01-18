<?php

namespace App\Filament\Resources\Gurus\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Models\User;

class GuruForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nip')
                    ->required()
                    ->unique(ignoreRecord: false),
                TextInput::make('nama')
                    ->required(),
                TextInput::make('email')
                    ->required()
                    ->email()
                    ->unique(table: User::class, ignoreRecord: false),
                TextInput::make('password')
                    ->password()
                    ->required(),
            ]);
    }
}
