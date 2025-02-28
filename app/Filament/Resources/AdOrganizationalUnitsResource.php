<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdOrganizationalUnitsResource\Pages;
use App\Filament\Resources\AdOrganizationalUnitsResource\RelationManagers;
use App\Models\AdOrganizationalUnits;
use App\Models\AdGroups;
use App\Models\AdUsers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Forms\Components\Section as FormSection;
use Filament\Forms\Components\Split;
use Filament\Notifications\Notification;

class AdOrganizationalUnitsResource extends Resource
{
    protected static ?string $model = AdOrganizationalUnits::class;

    protected static ?string $navigationIcon = 'heroicon-c-building-library';
    protected static ?string $navigationGroup = 'Domain';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Org Units';

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('User data')
                ->schema([
                    Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Basic Details')
                        ->columns(3)
                        ->icon('heroicon-o-document-magnifying-glass')
                        ->schema([
                            Infolists\Components\TextEntry::make('name'),
                            Infolists\Components\TextEntry::make('distinguishedname'),
                            Infolists\Components\TextEntry::make('description'),
                            Infolists\Components\TextEntry::make('whencreated'),
                            Infolists\Components\TextEntry::make('whenchanged'),
                            Infolists\Components\TextEntry::make('showinadvancedviewonly'),
                            Infolists\Components\TextEntry::make('objectcategory'),
                            Infolists\Components\TextEntry::make('iscriticalsystemobject'),
                            Infolists\Components\TextEntry::make('gplink')
                        ]),
                    ])
                ])
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
            ->headerActions([
                Tables\Actions\Action::make('Create OrganizationalUnit')
                ->visible(fn(): bool => auth()->user()->hasRole('super_admin'))
                ->icon('heroicon-o-plus')
                ->modalIcon('heroicon-o-plus')
                ->form([
                    FormSection::make([
                        Forms\Components\TextInput::make('name')
                        ->maxLength(50)
                        ->required(),
                        Forms\Components\TextInput::make('description')
                        ->maxLength(255)
                        ->required(),
                    ])->columns(2),
                ])->action(function ($data) {
                    try {
                        AdOrganizationalUnits::createOu($data);
                        Notification::make()
                            ->title('Created')
                            ->icon('heroicon-m-check-circle')
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Create Process Failed')
                            ->icon('heroicon-o-exclamation-triangle')
                            ->body($e->getMessage())
                            ->persistent()
                            ->danger()
                            ->send();
                    }
                    
                }),
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'view' => Pages\ViewAdOrganizationalUnits::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
