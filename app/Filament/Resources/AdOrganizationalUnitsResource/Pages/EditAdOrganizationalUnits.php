<?php

namespace App\Filament\Resources\AdOrganizationalUnitsResource\Pages;

use App\Filament\Resources\AdOrganizationalUnitsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdOrganizationalUnits extends EditRecord
{
    protected static string $resource = AdOrganizationalUnitsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
