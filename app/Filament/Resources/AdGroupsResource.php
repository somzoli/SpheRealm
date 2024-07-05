<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdGroupsResource\Pages;
use App\Filament\Resources\AdGroupsResource\RelationManagers;
use App\Models\AdGroups;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdGroupsResource extends Resource
{
    protected static ?string $model = AdGroups::class;

    protected static ?string $navigationIcon = 'solar-users-group-rounded-bold';
    protected static ?int $navigationSort = 1;
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
            'index' => Pages\ListAdGroups::route('/'),
            'create' => Pages\CreateAdGroups::route('/create'),
            'edit' => Pages\EditAdGroups::route('/{record}/edit'),
        ];
    }
}
