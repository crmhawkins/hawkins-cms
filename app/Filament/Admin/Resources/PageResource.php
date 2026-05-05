<?php

namespace App\Filament\Admin\Resources;

use App\Blocks\Registry;
use App\Filament\Admin\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
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

                Forms\Components\DateTimePicker::make('published_at')
                    ->label('Publicar el')
                    ->nullable()
                    ->helperText('Dejar vacío para publicar inmediatamente al guardar como publicada'),
            ])->columns(2),

            Forms\Components\Section::make('Bloques de contenido')->schema([
                Forms\Components\Repeater::make('blocks')
                    ->label('Bloques')
                    ->relationship('blocks')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Tipo de bloque')
                            ->options(Registry::options())
                            ->required()
                            ->live(),

                        Forms\Components\TextInput::make('sort')
                            ->label('Orden')
                            ->numeric()
                            ->default(0)
                            ->hidden(),

                        Forms\Components\Group::make()
                            ->schema(fn (Get $get): array => static::blockSchema($get('type') ?? ''))
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->reorderable('sort')
                    ->orderColumn('sort')
                    ->itemLabel(fn (array $state): ?string => Registry::label($state['type'] ?? '') ?: null)
                    ->collapsible()
                    ->addActionLabel('Añadir bloque'),
            ]),

            Forms\Components\Section::make('Presentación')->schema([
                Forms\Components\Select::make('header_variant')
                    ->label('Cabecera')
                    ->options([
                        'default'     => 'Predeterminada',
                        'dark'        => 'Oscura',
                        'transparent' => 'Transparente',
                        'minimal'     => 'Mínima',
                        'none'        => 'Sin cabecera',
                    ])
                    ->default('default'),

                Forms\Components\Select::make('footer_variant')
                    ->label('Pie de página')
                    ->options([
                        'default' => 'Predeterminado',
                        'minimal' => 'Mínimo',
                        'none'    => 'Sin pie de página',
                    ])
                    ->default('default'),
            ])->columns(2)->collapsible()->collapsed(),

            Forms\Components\Section::make('SEO')->schema([
                Forms\Components\TextInput::make('meta_title')
                    ->label('Meta título')
                    ->maxLength(70)
                    ->helperText('Dejar vacío para usar el título de la página')
                    ->placeholder('Título para buscadores (max 70 caracteres)'),

                Forms\Components\Textarea::make('meta_description')
                    ->label('Meta descripción')
                    ->maxLength(320)
                    ->rows(2)
                    ->placeholder('Descripción para buscadores (max 160 caracteres)'),

                Forms\Components\TextInput::make('og_image')
                    ->label('Imagen OG (URL o ruta)')
                    ->maxLength(500)
                    ->placeholder('/storage/og/pagina.jpg'),

                Forms\Components\Select::make('meta_robots')
                    ->label('Robots')
                    ->options([
                        'index, follow'     => 'Indexar y seguir enlaces (recomendado)',
                        'noindex, follow'   => 'No indexar',
                        'index, nofollow'   => 'Indexar pero no seguir enlaces',
                        'noindex, nofollow' => 'No indexar ni seguir',
                    ])
                    ->default('index, follow'),
            ])->columns(2)->collapsible()->collapsed(),
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
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft'     => 'warning',
                        default     => 'gray',
                    }),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publicada el')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Inmediata'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Page $record): string => url('/' . ($record->slug === 'home' ? '' : $record->slug)))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publicar seleccionadas')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update(['status' => 'published']))
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('draft')
                        ->label('Pasar a borrador')
                        ->icon('heroicon-o-pencil')
                        ->action(fn ($records) => $records->each->update(['status' => 'draft']))
                        ->requiresConfirmation(),
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

    private static function blockSchema(string $type): array
    {
        return match ($type) {
            'hero' => [
                Forms\Components\TextInput::make('content.title')->label('Título principal')->required(),
                Forms\Components\TextInput::make('content.subtitle')->label('Subtítulo'),
                Forms\Components\TextInput::make('content.image')->label('URL imagen de fondo'),
                Forms\Components\TextInput::make('content.button_text')->label('Texto del botón'),
                Forms\Components\TextInput::make('content.button_url')->label('URL del botón'),
            ],
            'gallery' => [
                Forms\Components\Textarea::make('content.images')
                    ->label('URLs de imágenes (una por línea)')
                    ->helperText('Pega una URL por línea')
                    ->rows(5)
                    ->dehydrateStateUsing(fn ($state) => is_array($state) ? $state : array_filter(array_map('trim', explode("\n", $state ?? '')))),
                Forms\Components\TextInput::make('content.columns')->label('Columnas')->numeric()->default(3)->minValue(1)->maxValue(6),
            ],
            'text_image' => [
                Forms\Components\TextInput::make('content.title')->label('Título'),
                Forms\Components\Textarea::make('content.body')->label('Texto')->rows(4),
                Forms\Components\TextInput::make('content.image')->label('URL de imagen'),
                Forms\Components\Select::make('content.image_position')->label('Posición imagen')
                    ->options(['right' => 'Derecha', 'left' => 'Izquierda'])->default('right'),
            ],
            'contact_form' => [
                Forms\Components\TextInput::make('content.title')->label('Título del formulario')->default('Contacto'),
                Forms\Components\TextInput::make('content.email')->label('Email de destino')->email(),
            ],
            'services' => [
                Forms\Components\TextInput::make('content.title')->label('Título de sección'),
                Forms\Components\Textarea::make('content.items')
                    ->label('Servicios (JSON array: [{title,description,icon}])')
                    ->rows(6)
                    ->helperText('Formato: [{"title":"Servicio 1","description":"Desc","icon":"star"}]'),
            ],
            'cta' => [
                Forms\Components\TextInput::make('content.title')->label('Título')->required(),
                Forms\Components\TextInput::make('content.subtitle')->label('Subtítulo'),
                Forms\Components\TextInput::make('content.button_text')->label('Texto del botón'),
                Forms\Components\TextInput::make('content.button_url')->label('URL del botón'),
            ],
            'map' => [
                Forms\Components\TextInput::make('content.address')->label('Dirección'),
                Forms\Components\TextInput::make('content.embed_url')->label('URL embed de Google Maps')->url(),
                Forms\Components\TextInput::make('content.zoom')->label('Zoom')->numeric()->default(15),
            ],
            'shop' => [
                Forms\Components\TextInput::make('content.title')->label('Título de sección')->default('Nuestra Tienda'),
                Forms\Components\Toggle::make('content.show_featured')->label('Solo productos destacados')->default(true),
                Forms\Components\TextInput::make('content.max_products')->label('Máximo de productos')->numeric()->default(6)->minValue(1)->maxValue(24),
            ],
            default => [
                Forms\Components\KeyValue::make('content')->label('Contenido')->reorderable(),
            ],
        };
    }
}
