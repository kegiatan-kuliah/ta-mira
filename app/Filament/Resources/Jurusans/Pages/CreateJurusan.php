<?php

namespace App\Filament\Resources\Jurusans\Pages;

use App\Filament\Resources\Jurusans\JurusanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJurusan extends CreateRecord
{
    protected static string $resource = JurusanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->title('Berhasil')
            ->body('Data berhasil tersimpan')
            ->success();
    }
}
