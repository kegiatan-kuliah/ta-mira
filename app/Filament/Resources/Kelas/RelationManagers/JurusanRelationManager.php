<?php

namespace App\Filament\Resources\Kelas\RelationManagers;

use App\Filament\Resources\Jurusans\JurusanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class JurusanRelationManager extends RelationManager
{
    protected static string $relationship = 'jurusan';

    protected static ?string $relatedResource = JurusanResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
