<?php

namespace App\Filament\Resources\SshKeysResource\Pages;

use App\Filament\Resources\SshKeysResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSshKeys extends ListRecords
{
    protected static string $resource = SshKeysResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
