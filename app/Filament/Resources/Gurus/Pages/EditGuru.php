<?php

namespace App\Filament\Resources\Gurus\Pages;

use App\Filament\Resources\Gurus\GuruResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditGuru extends EditRecord
{
    protected static string $resource = GuruResource::class;

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

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->title('Berhasil')
            ->body('Data berhasil diperbarui.')
            ->success();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return 'Edit Data Guru';
    }
}
