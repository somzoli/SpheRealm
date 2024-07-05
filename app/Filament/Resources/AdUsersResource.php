<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdUsersResource\Pages;
use App\Filament\Resources\AdUsersResource\RelationManagers;
use App\Models\AdUsers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdUsersResource extends Resource
{
    protected static ?string $model = AdUsers::class;

    protected static ?string $navigationIcon = 'solar-user-rounded-bold';
    protected static ?int $navigationSort = 0;
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
            'index' => Pages\ListAdUsers::route('/'),
            'create' => Pages\CreateAdUsers::route('/create'),
            'edit' => Pages\EditAdUsers::route('/{record}/edit'),
        ];
    }
}
