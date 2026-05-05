<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MenuResource\Pages;
use App\Filament\Admin\Resources\MenuResource\RelationManagers\ItemsRelationManager;
use App\Models\Menu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;
    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    protected static ?string $navigationLabel = 'Menús';
    protected static ?string $navigationGroup = 'Contenido';
    protected static ?string $modelLabel = 'Menú';
    protected static ?string $pluralModelLabel = 'Menús';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(255),

            Forms\Components\Select::make('location')
                ->label('Ubicación')
                ->options([
                    'header' => 'Cabecera',
                    'footer' => 'Pie de página',
                    'mobile' => 'Móvil',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('location')
                    ->label('Ubicación')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'header' => 'Cabecera',
                        'footer' => 'Pie de página',
                        'mobile' => 'Móvil',
                        default  => $state,
                    })
                    ->colors([
                        'primary' => 'header',
                        'secondary' => 'footer',
                        'success' => 'mobile',
                    ]),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Ítems')
                    ->counts('items'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
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

    public static function getRelationManagers(): array
    {
        return [
            ItemsRelationManager::class,
        ];
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
