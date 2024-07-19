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

    protected static ?string $navigationIcon = 'heroicon-c-building-library';
    protected static ?string $navigationGroup = 'Domain';
    protected static ?int $navigationSort = 3;

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
                Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable()
                ->badge(),
                Tables\Columns\TextColumn::make('distinguishedname')
                ->searchable()
                ->sortable()
                ->copyable()
                ->copyMessage('Copied')
                ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('description')
                ->searchable()
                ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                /*Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),*/
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
            //'create' => Pages\CreateAdOrgUnits::route('/create'),
            //'edit' => Pages\EditAdOrgUnits::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
