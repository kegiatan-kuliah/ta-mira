<?php

namespace App\Filament\Resources\Jurusans\Pages;

use App\Filament\Resources\Jurusans\JurusanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditJurusan extends EditRecord
{
    protected static string $resource = JurusanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->successNotification(
                Notification::make()
                    ->success()
                    ->title('Berhasil')
                    ->body('Data berhasil terhapus'),
            ),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Berhasil')
            ->body('Data berhasil diperbarui.')
            ->success();
    }
}
