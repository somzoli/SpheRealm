<?php

namespace App\Filament\Resources\AdOrganizationalUnitsResource\Pages;

use App\Filament\Resources\AdOrganizationalUnitsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdOrganizationalUnits extends ListRecords
{
    protected static string $resource = AdOrganizationalUnitsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
