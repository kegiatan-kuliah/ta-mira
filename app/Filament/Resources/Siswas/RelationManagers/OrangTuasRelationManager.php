<?php

namespace App\Filament\Resources\Siswas\RelationManagers;

use App\Filament\Resources\Siswas\SiswaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;

class OrangTuasRelationManager extends RelationManager
{
    protected static string $relationship = 'orangTuas';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return 'Orang Tua Siswa';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')->required(),
                TextInput::make('no_hp')->label('No Hp')->required(),
                TextInput::make('email')
                    ->label('Nama Pengguna')
                    ->required()
                    ->unique(table: User::class, ignoreRecord: false),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Select::make('type')
                    ->label('Pilih Hubungan')
                    ->required()
                    ->options([
                        'AYAH' => 'Ayah',
                        'IBU' => 'Ibu',
                        'WALI' => 'Wali'
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('no_hp')
                    ->label('No Hp')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Nama Pengguna')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('#')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Orang Tua')->mutateDataUsing(function (array $data) {
                    $user = User::create([
                        'email' => $data['email'],
                        'name' => $data['nama'],
                        'password' => bcrypt($data['password']),
                        'role' => 'orang tua'
                    ]);
                    $data['user_id'] = $user->id;

                    return $data;
                }),
            ])->recordActions([
                EditAction::make(),
                DeleteAction::make()->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Berhasil')
                        ->body('Data berhasil terhapus'),
                ),
            ])->emptyStateHeading('Tidak Ada Data');
    }
}
