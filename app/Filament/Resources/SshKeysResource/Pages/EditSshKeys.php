<?php

namespace App\Filament\Resources\SshKeysResource\Pages;

use App\Filament\Resources\SshKeysResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSshKeys extends EditRecord
{
    protected static string $resource = SshKeysResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
