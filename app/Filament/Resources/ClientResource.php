<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use App\Controller;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-s-computer-desktop';
    protected static ?string $navigationGroup = 'Domain';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Computers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->label('Client Name'),
                    Forms\Components\TextInput::make('ip')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->ipv4()
                    ->label('IP address'),
                    Forms\Components\Select::make('type')
                    ->required()
                    ->label('Type')
                    ->options([
                        'linux' => 'Linux',
                        'windows' => 'Windows',
                        'macos' => 'MacOs',
                    ]),
                    Forms\Components\TextInput::make('description')
                    ->label('Description'),
                    /*Forms\Components\TextInput::make('port')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(65535)
                    ->label('Port'),*/
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('ip')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('type')
                ->label('Type')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('description')
                ->searchable()
                ->sortable(),
                /*Tables\Columns\TextColumn::make('port')
                ->searchable()
                ->sortable(),*/
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('Create')
                ->visible(fn(): bool => auth()->user()->hasRole('super_admin'))
                ->icon('heroicon-o-user-plus')
                ->modalIcon('heroicon-o-user-plus')
                ->form([
                    Forms\Components\TextInput::make('name')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->label('Client Name'),
                    Forms\Components\TextInput::make('ip')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->ipv4()
                    ->label('IP address'),
                    Forms\Components\Select::make('type')
                    ->required()
                    ->label('Type')
                    ->options([
                        'linux' => 'Linux',
                        'windows' => 'Windows',
                        'macos' => 'MacOs',
                    ]),
                    Forms\Components\TextInput::make('description')
                    ->label('Description'),
                    /*Forms\Components\TextInput::make('port')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(65535)
                    ->label('Port'),*/
                ])
                ->action(function ($data, $model) {
                    $client = $model::create($data);
                    Notification::make()
                            ->title('Created')
                            ->icon('heroicon-m-check-circle')
                            ->success()
                            ->send();
                }),
                Tables\Actions\Action::make('import')
                ->visible(fn(): bool => auth()->user()->hasRole('super_admin'))
                ->label('Import Clients')
                ->action(function ($record) {
                    try {
                        // Run import in Helper
                        Controller::importServers();
                        Notification::make()
                                ->title('Client Import Done')
                                ->icon('heroicon-m-check-circle')
                                ->persistent()
                                ->success()
                                ->send();
                    } catch (\Throwable $e) {
                        // Show error message to user
                        Notification::make()
                                ->title('Connection error')
                                ->icon('heroicon-o-exclamation-triangle')
                                ->body($e->getMessage())
                                ->persistent()
                                ->danger()
                                ->send();
                    }
                })
                ->icon('heroicon-s-inbox-arrow-down')
                ->color('warning'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            //'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
    public static function canCreate(): bool
    {
        return false;
    }
}
