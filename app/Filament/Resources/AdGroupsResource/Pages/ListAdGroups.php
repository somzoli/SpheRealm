<?php

namespace App\Filament\Resources\AdGroupsResource\Pages;

use App\Filament\Resources\AdGroupsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdGroups extends ListRecords
{
    protected static string $resource = AdGroupsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
