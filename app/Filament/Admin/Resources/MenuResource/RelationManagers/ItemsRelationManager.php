<?php

namespace App\Filament\Admin\Resources\MenuResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title = 'Ítems de menú';
    protected static ?string $modelLabel = 'ítem';
    protected static ?string $pluralModelLabel = 'ítems';

    public function form(Form $form): Form
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

    public function table(Table $table): Table
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
