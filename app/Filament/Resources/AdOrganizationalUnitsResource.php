<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdOrganizationalUnitsResource\Pages;
use App\Filament\Resources\AdOrganizationalUnitsResource\RelationManagers;
use App\Models\AdOrganizationalUnits;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdOrganizationalUnitsResource extends Resource
{
    protected static ?string $model = AdOrganizationalUnits::class;

    protected static ?string $navigationIcon = 'fluentui-organization-12-o';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Domain Realm';

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
            'index' => Pages\ListAdOrganizationalUnits::route('/'),
            'create' => Pages\CreateAdOrganizationalUnits::route('/create'),
            'edit' => Pages\EditAdOrganizationalUnits::route('/{record}/edit'),
        ];
    }
}
