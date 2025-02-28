<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdGroupsResource\Pages;
use App\Filament\Resources\AdGroupsResource\RelationManagers;
use App\Models\AdGroups;
use App\Models\AdUsers;
use App\Models\AdOrganizationalUnits;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
//use Illuminate\Database\Eloquent\Builder;
use LdapRecord\Query\Model\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Forms\Components\Section as FormSection;
use Filament\Forms\Components\Split;
use Filament\Notifications\Notification;

class AdGroupsResource extends Resource
{
    protected static ?string $model = AdGroups::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Domain';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Groups';

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
                            Infolists\Components\TextEntry::make('samaccountname'),
                            Infolists\Components\TextEntry::make('mail'),
                            Infolists\Components\TextEntry::make('proxyaddresses'),
                            Infolists\Components\TextEntry::make('whencreated'),
                            Infolists\Components\TextEntry::make('whenchanged'),
                            Infolists\Components\TextEntry::make('iscriticalsystemobject'),
                            Infolists\Components\TextEntry::make('distinguishedname'),
                            Infolists\Components\TextEntry::make('objectcategory'),
                            Infolists\Components\TextEntry::make('objectclass')
                            ->html(),
                            Infolists\Components\TextEntry::make('gidnumber'),
                        ]),
                        Tabs\Tab::make('Members')
                        ->icon('heroicon-o-user-group')
                        ->schema([
                            Infolists\Components\TextEntry::make('member')
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
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable()
                ->badge(),
                Tables\Columns\TextColumn::make('mail')
                ->searchable()
                ->sortable()
                ->icon('heroicon-m-envelope')
                ->copyable()
                ->copyMessage('Copied')
                ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('proxyaddresses')
                ->searchable()
                ->sortable()
                ->wrap()
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
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('Create Group')
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
                        Forms\Components\TextInput::make('email')
                        ->maxLength(255)
                        ->required(),
                    ])->columns(2),
                    FormSection::make([
                        Forms\Components\Select::make('users')
                        ->options(AdUsers::allUsers())
                        ->multiple()
                        ->required()
                        ->preload()
                        ->searchable(),
                        Forms\Components\Select::make('organizational_unit')
                        ->options(AdOrganizationalUnits::allOus())
                        ->required()
                        ->preload()
                        ->searchable()
                    ])->columns(2),
                ])->action(function ($data) {
                    try {
                        AdGroups::createGroup($data);
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
            'index' => Pages\ListAdGroups::route('/'),
            //'create' => Pages\CreateAdGroups::route('/create'),
            //'edit' => Pages\EditAdGroups::route('/{record}/edit'),
            'view' => Pages\ViewAdGroups::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
