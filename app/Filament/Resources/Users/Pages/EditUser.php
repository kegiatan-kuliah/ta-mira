<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

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

    protected function getSavedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->title('berhasil')
            ->body('Data berhasil diperbarui.')
            ->success();
    }

    public function getTitle(): string
    {
        return 'Edit Data Operator';
    }
}
