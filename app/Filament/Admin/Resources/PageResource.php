<?php

namespace App\Filament\Admin\Resources;

use App\Blocks\Registry;
use App\Filament\Admin\Resources\PageResource\Pages;
use App\Models\Block;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Páginas';
    protected static ?string $modelLabel = 'Página';
    protected static ?string $pluralModelLabel = 'Páginas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información básica')->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),

                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(Page::class, 'slug', ignoreRecord: true),

                Forms\Components\Select::make('status')
                    ->label('Estado')
                    ->options(['draft' => 'Borrador', 'published' => 'Publicada'])
                    ->default('draft')
                    ->required(),
            ])->columns(3),

            Forms\Components\Section::make('Bloques de contenido')->schema([
                Forms\Components\Repeater::make('blocks')
                    ->label('Bloques')
                    ->relationship('blocks')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Tipo de bloque')
                            ->options(Registry::options())
                            ->required()
                            ->reactive(),

                        Forms\Components\TextInput::make('sort')
                            ->label('Orden')
                            ->numeric()
                            ->default(0)
                            ->hidden(),

                        Forms\Components\KeyValue::make('content')
                            ->label('Contenido')
                            ->keyLabel('Campo')
                            ->valueLabel('Valor')
                            ->reorderable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->reorderable('sort')
                    ->orderColumn('sort')
                    ->itemLabel(fn (array $state): ?string => Registry::label($state['type'] ?? '') ?: null)
                    ->collapsible()
                    ->addActionLabel('Añadir bloque'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors(['warning' => 'draft', 'success' => 'published']),
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

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit'   => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
