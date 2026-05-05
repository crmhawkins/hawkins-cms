<?php

namespace App\Filament\Hawkins\Resources;

use App\Filament\Hawkins\Resources\TenantResource\Pages;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Tenants';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('id')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\Select::make('theme')->options(['sanzahra' => 'Sanzahra'])->default('sanzahra'),
            Forms\Components\Toggle::make('ecommerce_enabled')->label('E-commerce activo'),
            Forms\Components\Select::make('header_layout')
                ->options(['center' => 'Centrado', 'left' => 'Izquierda', 'right' => 'Derecha'])
                ->default('center'),
            Forms\Components\Select::make('payment_gateway')
                ->options(['none' => 'Sin pasarela', 'stripe_connect' => 'Stripe Connect', 'redsys' => 'Redsys', 'paypal' => 'PayPal'])
                ->default('none'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('theme'),
                Tables\Columns\IconColumn::make('ecommerce_enabled')->boolean(),
                Tables\Columns\TextColumn::make('payment_gateway'),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
