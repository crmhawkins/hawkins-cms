<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\MenuResource\Pages;
use App\Models\MenuItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $model = MenuItem::class;
    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    protected static ?string $navigationLabel = 'Menú';
    protected static ?string $modelLabel = 'Ítem de menú';
    protected static ?string $pluralModelLabel = 'Ítems de menú';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('label')
                ->label('Etiqueta')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('url')
                ->label('URL')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('sort')
                ->label('Orden')
                ->numeric()
                ->default(0),

            Forms\Components\Select::make('parent_id')
                ->label('Ítem padre')
                ->relationship('parent', 'label')
                ->searchable()
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort')
                    ->label('Orden')
                    ->sortable(),
                Tables\Columns\TextColumn::make('label')
                    ->label('Etiqueta')
                    ->searchable(),
                Tables\Columns\TextColumn::make('url')
                    ->label('URL'),
                Tables\Columns\TextColumn::make('parent.label')
                    ->label('Padre')
                    ->default('—'),
            ])
            ->defaultSort('sort')
            ->reorderable('sort')
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

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit'   => Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }
}
