<?php

namespace App\Filament\Resources\AdUsersResource\Pages;

use App\Filament\Resources\AdUsersResource;
use App\Models\AdUsers;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateAdUsers extends CreateRecord
{
    protected static string $resource = AdUsersResource::class;

    protected function getFormActions(): array
    {
        return [
            Action::make('Create')->action(function ($data) {
                try {
                    AdUsers::createUser($data);
                    Notification::make()
                        ->title('Created')
                        ->icon('heroicon-m-check-circle')
                        ->success()
                        ->send();
                } catch (\Throwable $e) {
                    Notification::make()
                        ->title('Create Process Failed')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->body($e->getMessage())
                        ->persistent()
                        ->danger()
                        ->send();
                }
            }),
        ];
    }
}
