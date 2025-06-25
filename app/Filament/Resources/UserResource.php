<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\MA\MA_Usuarios;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $modelLabel = 'Usuarios';
    protected static ?string $navigationGroup = 'Administracion';

    protected static ?string $navigationLabel = "Usuarios API";

    //protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(191),
                Forms\Components\Select::make('brand_id')
                    ->relationship('management', 'Gerencia')
                    ->required(),
                Forms\Components\Toggle::make('state')->label('Activo')
                    ->inline(false)
                    ->onIcon('heroicon-m-bolt')
                    ->offIcon('heroicon-s-user')
                    ->required(),
                Forms\Components\Select::make('userRomaID')
                    ->options(fn() => MA_Usuarios::where('Activo', 1)->pluck('Nombre', 'ID'))
                    ->searchable(),
                Forms\Components\Select::make('role')
                    ->options([
                        'user' => 'user',
                        'salvin' => 'salvin',
                        'marketing' => 'marketing',
                        'analista' => 'analista',
                        'admin' => 'admin',
                    ])
                    ->default('user')
                    ->disablePlaceholderSelection()
                    ->required(),
//                Forms\Components\DateTimePicker::make('email_verified_at'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->dehydrated(fn($state) => filled($state))
                    ->maxLength(191),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name')->label('Nombre')
                ->searchable(),
                Tables\Columns\TextColumn::make('email')
                ->searchable(),
                Tables\Columns\TextColumn::make('management.Gerencia')->label('Gerencia'),
                Tables\Columns\ToggleColumn::make('state')->label('Estado')
                    ->disabled(!Auth::user()->isAdmin())
                    ->onIcon('heroicon-m-bolt')
                    ->offIcon('heroicon-s-user'),
                Tables\Columns\TextColumn::make('role')->label('Rol'),
//                Tables\Columns\TextColumn::make('email_verified_at')
//                    ->dateTime(),
//                Tables\Columns\TextColumn::make('created_at')
//                    ->dateTime(),
//                Tables\Columns\TextColumn::make('updated_at')
//                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
