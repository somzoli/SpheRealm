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
use LdapRecord\Models\ActiveDirectory\User;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;

class AdUsersResource extends Resource
{
    protected static ?string $model = AdUsers::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Domain';
    protected static ?int $navigationSort = 0;
    protected static ?string $navigationLabel = 'Accounts';

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
                            Infolists\Components\ImageEntry::make('jpegphoto')
                                ->label('Photo')
                                ->height(100)
                                ->circular()
                                ->checkFileExistence(false)
                                ->disk('public')
                                ->columnSpanFull()
                                ->visible(fn($record):bool => !empty($record->jpegphoto)),
                            Infolists\Components\TextEntry::make('samaccountname'),
                            Infolists\Components\TextEntry::make('mail'),
                            Infolists\Components\TextEntry::make('displayname'),
                            Infolists\Components\TextEntry::make('otherMailbox'),
                            Infolists\Components\TextEntry::make('lastlogon'),
                            Infolists\Components\TextEntry::make('accountexpires'),
                            Infolists\Components\TextEntry::make('userprincipalname'),
                            Infolists\Components\TextEntry::make('distinguishedname'),
                            Infolists\Components\TextEntry::make('uidnumber'),
                            Infolists\Components\TextEntry::make('whenchanged'),
                            Infolists\Components\TextEntry::make('badpwdcount'),
                            Infolists\Components\TextEntry::make('loginshell'),
                            Infolists\Components\TextEntry::make('active')
                            ->badge(),
                        ]),
                        Tabs\Tab::make('Membership')
                        ->icon('heroicon-o-user-group')
                        ->schema([
                            Infolists\Components\TextEntry::make('memberof')
                                ->columnSpanFull()
                                ->listWithLineBreaks()
                                ->html(), 
                        ]),
                    ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordClasses(fn ($record) => match ($record->active) {
                'False' => 'opacity-60 border-l-2 !border-l-orange-600 !dark:border-l-orange-300',
                'True' => 'border-l-2 !border-l-green-600 !dark:border-l-green-300',
                default => null,
            })
            ->columns([
                ImageColumn::make('jpegphoto')
                ->label('Photo')
                ->circular(),
                Tables\Columns\TextColumn::make('samaccountname')
                ->searchable()
                ->sortable()
                ->badge(),
                Tables\Columns\TextColumn::make('active')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('mail')
                ->searchable()
                ->sortable()
                ->icon('heroicon-m-envelope')
                ->copyable()
                ->copyMessage('Copied')
                ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('otherMailbox')
                ->searchable()
                ->sortable()
                ->icon('heroicon-m-envelope')
                ->copyable()
                ->copyMessage('Copied')
                ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('description')
                ->searchable()
                ->sortable()
                ->wrap(),
                Tables\Columns\TextColumn::make('distinguishedname')
                ->searchable()
                ->sortable()
                ->wrap()
                ->copyable()
                ->copyMessage('Copied')
                ->copyMessageDuration(1500)
            ])
            ->filters([
                Filters\SelectFilter::make('active')
                ->options([
                    'True' => 'Active',
                    'False' => 'Disabled',
                ])->attribute('active')
                ->label('Active Status'),
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
            'index' => Pages\ListAdUsers::route('/'),
            //'create' => Pages\CreateAdUsers::route('/create'),
            //'view' => Pages\EditAdUsers::route('/{record}/edit'),
            'view' => Pages\ViewAdUser::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
