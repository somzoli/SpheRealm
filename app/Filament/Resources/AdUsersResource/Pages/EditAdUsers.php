<?php

namespace App\Filament\Resources\AdUsersResource\Pages;

use App\Filament\Resources\AdUsersResource;
use App\Models\AdUsers;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditAdUsers extends EditRecord
{
    protected static string $resource = AdUsersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('Update')->action(function ($data) {
                try {
                    AdUsers::updateUser($data);
                    Notification::make()
                        ->title('Updated')
                        ->icon('heroicon-m-check-circle')
                        ->success()
                        ->send();
                } catch (\Throwable $e) {
                    Notification::make()
                        ->title('Update Process Failed')
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
