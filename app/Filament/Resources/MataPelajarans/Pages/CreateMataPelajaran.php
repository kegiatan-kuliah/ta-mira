<?php

namespace App\Filament\Resources\MataPelajarans\Pages;

use App\Filament\Resources\MataPelajarans\MataPelajaranResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMataPelajaran extends CreateRecord
{
    protected static string $resource = MataPelajaranResource::class;

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

    public function getTitle(): string
    {
        return 'Tambah Data Mata Pelajaran';
    }
}
