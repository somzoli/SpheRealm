<?php

namespace App\Filament\Resources\AdUsersResource\Pages;

use App\Filament\Resources\AdUsersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdUsers extends EditRecord
{
    protected static string $resource = AdUsersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
