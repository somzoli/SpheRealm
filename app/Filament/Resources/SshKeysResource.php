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
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Infolists\Components\Section;

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
                Forms\Components\Section::make()
                ->description('Properties')
                ->columns(1)
                ->schema([
                    Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),
                    Forms\Components\TextInput::make('username')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('description')
                    ->maxLength(255),
                ])->collapsible()->persistCollapsed(),
                Forms\Components\Section::make()
                ->description('Key Data')
                ->columns(2)
                ->schema([
                    Forms\Components\Textarea::make('private')
                    ->label('Private KEY content')
                    ->required()
                    ->maxLength(5000)
                    ->autosize()
                    ->unique(ignoreRecord: true),
                    Forms\Components\Textarea::make('public')
                    ->label('Public KEY content')
                    ->maxLength(5000)
                    ->autosize()
                    ->unique(ignoreRecord: true),
                ])->collapsible()->persistCollapsed()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('username')
                ->searchable()
                ->sortable()
                ->copyable()
                ->copyMessage('Data copied')
                ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('description')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('private')
                ->searchable()
                ->sortable()
                ->wrap()
                ->limit(30)
                ->copyable()
                ->copyMessage('Data copied')
                ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('public')
                ->searchable()
                ->sortable()
                ->wrap()
                ->limit(30)
                ->copyable()
                ->copyMessage('Data copied')
                ->copyMessageDuration(1500),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
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
            //'create' => Pages\CreateSshKeys::route('/create'),
            //'edit' => Pages\EditSshKeys::route('/{record}/edit'),
        ];
    }
}
