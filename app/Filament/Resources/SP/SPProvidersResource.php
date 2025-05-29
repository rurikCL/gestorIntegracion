<?php

namespace App\Filament\Resources\SP;

use App\Filament\Resources\SP\SPProvidersResource\Pages;
use App\Filament\Resources\SP\SPProvidersResource\RelationManagers;
use App\Models\SP\SP_providers;
use App\Models\SP\SPProviders;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SPProvidersResource extends Resource
{
    protected static ?string $model = SP_providers::class;

    //protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Ordenes de Compra';
    protected static ?string $modelLabel = 'Proveedor';
    protected static ?string $navigationLabel = 'Proveedores';
    protected static ?string $pluralLabel = 'Proveedores';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make("name")->label("Nombre")->required(),
                    Forms\Components\TextInput::make("rut")->required(),
                    Forms\Components\TextInput::make("address")->label("Direccion")->required(),
                    Forms\Components\TextInput::make("city")->label("Ciudad")->required(),
                    Forms\Components\TextInput::make("postal_code")->label("Codigo Postal"),
                    Forms\Components\TextInput::make("payment_condition")->label("Condicion pago (dias)")->numeric(),
                    Forms\Components\TextInput::make("contact")->label("Contacto"),
                    Forms\Components\TextInput::make("phone")->label("Telefono"),
                    Forms\Components\TextInput::make("email")->label("Email"),
                    Forms\Components\TextInput::make("cuenta"),
                    Forms\Components\TextInput::make("costCenter")->label("Centro Costo"),
                    Forms\Components\TextInput::make("gasto"),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("name")->searchable()->sortable(),
                Tables\Columns\TextColumn::make("rut")->searchable(),
                Tables\Columns\TextColumn::make("address")->searchable(),
                Tables\Columns\TextColumn::make("phone"),
                Tables\Columns\TextColumn::make("email"),
                Tables\Columns\TextColumn::make("cuenta"),
                Tables\Columns\TextColumn::make("costCenter")->sortable(),
                Tables\Columns\TextColumn::make("gasto"),
                Tables\Columns\TextColumn::make("created_at"),
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
            'index' => Pages\ListSPProviders::route('/'),
            'create' => Pages\CreateSPProviders::route('/create'),
            'edit' => Pages\EditSPProviders::route('/{record}/edit'),
        ];
    }
}
