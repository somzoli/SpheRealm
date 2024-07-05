<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SshKeysResource\Pages;
use App\Filament\Resources\SshKeysResource\RelationManagers;
use App\Models\SshKeys;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SshKeysResource extends Resource
{
    protected static ?string $model = SshKeys::class;

    protected static ?string $navigationIcon = 'solar-key-bold';
    protected static ?int $navigationSort = 0;
    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSshKeys::route('/'),
            'create' => Pages\CreateSshKeys::route('/create'),
            'edit' => Pages\EditSshKeys::route('/{record}/edit'),
        ];
    }
}