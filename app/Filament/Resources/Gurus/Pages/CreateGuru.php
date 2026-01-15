<?php

namespace App\Filament\Resources\Gurus\Pages;

use App\Filament\Resources\Gurus\GuruResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;

class CreateGuru extends CreateRecord
{
    protected static string $resource = GuruResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = User::create([
            'email' => $data['email'],
            'name' => $data['nama'],
            'password' => bcrypt($data['password']),
            'role' => 'guru'
        ]);
        $data['user_id'] = $user->id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return 'Tambah Data Guru';
    }

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->title('Berhasil')
            ->body('Data berhasil tersimpan')
            ->success();
    }
}
