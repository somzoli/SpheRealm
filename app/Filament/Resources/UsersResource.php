<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsersResource\Pages;
use App\Filament\Resources\UsersResource\RelationManagers;
use App\Models\Users;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Spatie\Permission\Models\Role;
use App\Controller;

class UsersResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'solar-shield-user-bold';
    protected static ?int $navigationSort = 0;
    protected static ?string $navigationGroup = 'Access Shield';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                ->description('Edit')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('name')
                    ->readonly()
                    ->dehydrated(false),
                    Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
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
                ->label('Username'),
                Tables\Columns\TextColumn::make('realname')
                ->searchable()
                ->sortable()
                ->label('Name'),
                Tables\Columns\TextColumn::make('email')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('domain')
                ->badge()
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('Roles.name')
                ->label('Roles')
                ->searchable(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('Create')
                ->visible(fn(): bool => auth()->user()->hasRole('super_admin'))
                ->icon('heroicon-o-user-plus')
                ->modalIcon('heroicon-o-user-plus')
                ->form([
                    Forms\Components\TextInput::make('name')
                    ->label('Username')
                    ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('email')
                    ->maxLength(255)
                    ->email()
                    ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('password')
                    ->required()
                    ->maxLength(255)
                    ->password(),
                ])
                ->action(function ($data, $model) {
                    $user = $model::create($data);
                    Notification::make()
                            ->title('Created')
                            ->icon('heroicon-m-check-circle')
                            ->success()
                            ->send();
                }),
                Tables\Actions\Action::make('import')
                ->visible(fn(): bool => auth()->user()->hasRole('super_admin'))
                ->label('Import Users')
                ->action(function ($record) {
                    try {
                        // Run import in Helper
                        Controller::importUsers();
                        Notification::make()
                                ->title('Import done')
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
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('Change Password')
                ->color('warning')
                ->icon('heroicon-m-lock-closed')
                ->modalIcon('heroicon-m-lock-closed')
                ->visible(fn ($record) => (empty($record->domain) && auth()->user()->hasRole('super_admin')))
                ->form([
                    Forms\Components\TextInput::make('password')
                    ->maxLength(255)
                    ->password()
                    ->required(),
                ])
                ->action(function (array $data, $record): void {
                    $record->update(['password' => $data['password']]);
                    Notification::make()
                            ->title('Password changed')
                            ->icon('heroicon-m-check-circle')
                            ->success()
                            ->send();
                }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('assign')
                    ->visible(fn(): bool => auth()->user()->hasRole('super_admin'))
                    ->label('Assig Roles')
                    ->icon('heroicon-o-shield-exclamation')
                    ->modalIcon('heroicon-o-shield-exclamation')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('role_name')
                            //->relationship('roles', 'name')
                            ->options(Role::query()->pluck('name', 'id'))
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->live()
                    ])
                    ->action(function ($data, $records): void {
                        foreach ($data['role_name'] as $id) {
                            $rolename[] = Role::where('id', $id)->first()->name;
                        }
                        foreach ($records as $record) {
                            $record->syncRoles($rolename);
                        }
                        Notification::make()
                        ->title('Saved successfully')
                        ->success()
                        ->send();
                    }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
    public static function canCreate(): bool
    {
        return false;
    }
}
