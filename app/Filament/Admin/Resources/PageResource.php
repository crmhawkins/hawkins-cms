<?php

namespace App\Filament\Admin\Resources;

use App\Blocks\Registry;
use App\Filament\Admin\Resources\PageResource\Pages;
use App\Models\Footer;
use App\Models\Header;
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

                        Forms\Components\Section::make('⚙️ Layout del bloque')
                            ->schema([
                                Forms\Components\Grid::make(4)->schema([
                                    Forms\Components\ColorPicker::make('bg_color')
                                        ->label('Fondo'),
                                    Forms\Components\ColorPicker::make('text_color')
                                        ->label('Color texto'),
                                    Forms\Components\Select::make('container_width')
                                        ->label('Ancho contenido')
                                        ->options([
                                            'full'   => 'Ancho completo',
                                            'wide'   => 'Ancho (1400px)',
                                            'normal' => 'Normal (1200px)',
                                            'narrow' => 'Estrecho (800px)',
                                        ])
                                        ->default('normal'),
                                    Forms\Components\Toggle::make('full_width')
                                        ->label('Sin container'),
                                ]),
                                Forms\Components\Grid::make(5)->schema([
                                    Forms\Components\TextInput::make('padding_top')
                                        ->label('Relleno superior (px)')
                                        ->numeric()->default(0)->suffix('px'),
                                    Forms\Components\TextInput::make('padding_bottom')
                                        ->label('Relleno inferior (px)')
                                        ->numeric()->default(0)->suffix('px'),
                                    Forms\Components\TextInput::make('padding_x')
                                        ->label('Relleno lateral (px)')
                                        ->numeric()->default(0)->suffix('px'),
                                    Forms\Components\TextInput::make('margin_top')
                                        ->label('Margen superior (px)')
                                        ->numeric()->default(0)->suffix('px'),
                                    Forms\Components\TextInput::make('margin_bottom')
                                        ->label('Margen inferior (px)')
                                        ->numeric()->default(0)->suffix('px'),
                                ]),
                                Forms\Components\Grid::make(3)->schema([
                                    Forms\Components\Select::make('separator_top')
                                        ->label('Separador superior')
                                        ->options([
                                            'none'     => 'Ninguno',
                                            'wave'     => 'Ola',
                                            'diagonal' => 'Diagonal',
                                            'curve'    => 'Curva',
                                            'triangle' => 'Triángulo',
                                        ])->default('none'),
                                    Forms\Components\Select::make('separator_bottom')
                                        ->label('Separador inferior')
                                        ->options([
                                            'none'     => 'Ninguno',
                                            'wave'     => 'Ola',
                                            'diagonal' => 'Diagonal',
                                            'curve'    => 'Curva',
                                            'triangle' => 'Triángulo',
                                        ])->default('none'),
                                    Forms\Components\ColorPicker::make('separator_color')
                                        ->label('Color separador'),
                                ]),
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('css_class')
                                        ->label('Clases CSS adicionales')
                                        ->placeholder('mi-clase otra-clase'),
                                    Forms\Components\Textarea::make('custom_css')
                                        ->label('CSS personalizado del bloque')
                                        ->rows(3)
                                        ->placeholder('font-size: 1.1rem; letter-spacing: .05em;'),
                                ]),
                            ])
                            ->collapsible()
                            ->collapsed()
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

                Forms\Components\Select::make('header_id')
                    ->label('Header personalizado')
                    ->relationship('header', 'name')
                    ->placeholder('— Usar predeterminado del sitio —')
                    ->searchable()
                    ->preload()
                    ->helperText('Si no seleccionas uno, se usará el header predeterminado'),

                Forms\Components\Select::make('footer_id')
                    ->label('Footer personalizado')
                    ->relationship('footer', 'name')
                    ->placeholder('— Usar predeterminado del sitio —')
                    ->searchable()
                    ->preload()
                    ->helperText('Si no seleccionas uno, se usará el footer predeterminado'),
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

            Forms\Components\Section::make('CSS / JS personalizado')
                ->schema([
                    Forms\Components\Textarea::make('custom_css')
                        ->label('CSS personalizado')
                        ->rows(6)
                        ->placeholder('/* CSS solo para esta página */')
                        ->helperText('Se inyecta en el <head> solo en esta página'),
                    Forms\Components\Textarea::make('custom_js')
                        ->label('JavaScript personalizado')
                        ->rows(6)
                        ->placeholder('// JS solo para esta página')
                        ->helperText('Se inyecta antes de </body> solo en esta página'),
                ])->collapsible()->collapsed(),
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
            'testimonials' => [
                Forms\Components\TextInput::make('content.title')->label('Título sección'),
                Forms\Components\Repeater::make('content.items')
                    ->label('Testimonios')
                    ->schema([
                        Forms\Components\TextInput::make('name')->label('Nombre'),
                        Forms\Components\TextInput::make('role')->label('Cargo/Empresa'),
                        Forms\Components\Textarea::make('text')->label('Testimonio')->rows(3),
                        Forms\Components\TextInput::make('photo')->label('Foto URL'),
                        Forms\Components\Select::make('rating')->label('Puntuación')->options([1=>'⭐',2=>'⭐⭐',3=>'⭐⭐⭐',4=>'⭐⭐⭐⭐',5=>'⭐⭐⭐⭐⭐'])->default(5),
                    ])->columns(2)->defaultItems(1),
            ],
            'faq' => [
                Forms\Components\TextInput::make('content.title')->label('Título sección'),
                Forms\Components\TextInput::make('content.subtitle')->label('Subtítulo'),
                Forms\Components\Repeater::make('content.items')
                    ->label('Preguntas')
                    ->schema([
                        Forms\Components\TextInput::make('question')->label('Pregunta')->required(),
                        Forms\Components\Textarea::make('answer')->label('Respuesta')->rows(3)->required(),
                    ])->defaultItems(1),
            ],
            'team' => [
                Forms\Components\TextInput::make('content.title')->label('Título sección'),
                Forms\Components\TextInput::make('content.subtitle')->label('Subtítulo'),
                Forms\Components\Repeater::make('content.items')
                    ->label('Miembros del equipo')
                    ->schema([
                        Forms\Components\TextInput::make('name')->label('Nombre'),
                        Forms\Components\TextInput::make('role')->label('Cargo'),
                        Forms\Components\Textarea::make('bio')->label('Bio corta')->rows(2),
                        Forms\Components\TextInput::make('photo')->label('Foto URL'),
                        Forms\Components\TextInput::make('linkedin')->label('LinkedIn URL'),
                        Forms\Components\TextInput::make('instagram')->label('Instagram URL'),
                    ])->columns(2)->defaultItems(1),
            ],
            'video' => [
                Forms\Components\TextInput::make('content.title')->label('Título'),
                Forms\Components\TextInput::make('content.subtitle')->label('Subtítulo'),
                Forms\Components\TextInput::make('content.video_url')->label('URL YouTube o Vimeo')->required(),
                Forms\Components\TextInput::make('content.cover_image')->label('Imagen de portada URL'),
                Forms\Components\Toggle::make('content.autoplay')->label('Reproducción automática (con silencio)'),
            ],
            'counter' => [
                Forms\Components\TextInput::make('content.title')->label('Título sección'),
                Forms\Components\ColorPicker::make('content.bg_color')->label('Color de fondo')->default('#111111'),
                Forms\Components\Repeater::make('content.items')
                    ->label('Estadísticas')
                    ->schema([
                        Forms\Components\TextInput::make('number')->label('Número (ej: 200+)')->required(),
                        Forms\Components\TextInput::make('label')->label('Etiqueta')->required(),
                        Forms\Components\TextInput::make('icon')->label('Icono emoji'),
                    ])->columns(3)->defaultItems(3),
            ],
            'accordion' => [
                Forms\Components\TextInput::make('content.title')->label('Título sección'),
                Forms\Components\Repeater::make('content.items')
                    ->label('Items del acordeón')
                    ->schema([
                        Forms\Components\TextInput::make('heading')->label('Título')->required(),
                        Forms\Components\Textarea::make('body')->label('Contenido')->rows(3)->required(),
                        Forms\Components\Toggle::make('open')->label('Abierto por defecto'),
                    ])->defaultItems(1),
            ],
            'pricing' => [
                Forms\Components\TextInput::make('content.title')->label('Título sección'),
                Forms\Components\TextInput::make('content.subtitle')->label('Subtítulo'),
                Forms\Components\Repeater::make('content.items')
                    ->label('Planes')
                    ->schema([
                        Forms\Components\TextInput::make('name')->label('Nombre del plan')->required(),
                        Forms\Components\TextInput::make('price')->label('Precio (ej: 49€/mes)')->required(),
                        Forms\Components\TextInput::make('description')->label('Descripción corta'),
                        Forms\Components\Textarea::make('features')->label('Características (una por línea)')->rows(5),
                        Forms\Components\TextInput::make('cta_text')->label('Texto botón'),
                        Forms\Components\TextInput::make('cta_url')->label('URL botón'),
                        Forms\Components\Toggle::make('highlighted')->label('Plan destacado'),
                    ])->columns(2)->defaultItems(1),
            ],
            'timeline' => [
                Forms\Components\TextInput::make('content.title')->label('Título sección'),
                Forms\Components\Repeater::make('content.items')
                    ->label('Eventos')
                    ->schema([
                        Forms\Components\TextInput::make('year')->label('Año / Fecha')->required(),
                        Forms\Components\TextInput::make('title')->label('Título')->required(),
                        Forms\Components\Textarea::make('description')->label('Descripción')->rows(2),
                        Forms\Components\TextInput::make('image')->label('Imagen URL'),
                    ])->columns(2)->defaultItems(1),
            ],
            'logo_grid' => [
                Forms\Components\TextInput::make('content.title')->label('Título sección'),
                Forms\Components\TextInput::make('content.subtitle')->label('Subtítulo'),
                Forms\Components\Repeater::make('content.logos')
                    ->label('Logos')
                    ->schema([
                        Forms\Components\TextInput::make('image')->label('URL imagen')->required(),
                        Forms\Components\TextInput::make('alt')->label('Nombre empresa'),
                        Forms\Components\TextInput::make('url')->label('URL (opcional)'),
                    ])->columns(3)->defaultItems(1),
            ],
            'banner' => [
                Forms\Components\TextInput::make('content.text')->label('Texto del banner')->required(),
                Forms\Components\TextInput::make('content.cta_text')->label('Texto botón CTA'),
                Forms\Components\TextInput::make('content.cta_url')->label('URL botón CTA'),
                Forms\Components\ColorPicker::make('content.bg_color')->label('Color fondo')->default('#c9a96e'),
                Forms\Components\ColorPicker::make('content.text_color')->label('Color texto')->default('#ffffff'),
                Forms\Components\Toggle::make('content.dismissible')->label('Se puede cerrar'),
            ],
            'text' => [
                Forms\Components\TextInput::make('content.title')->label('Título (opcional)'),
                Forms\Components\Select::make('content.title_align')->label('Alineación título')
                    ->options(['left'=>'Izquierda','center'=>'Centro','right'=>'Derecha'])->default('left'),
                Forms\Components\Textarea::make('content.body')->label('Contenido (texto)')->rows(8)->required(),
                Forms\Components\Select::make('content.text_align')->label('Alineación texto')
                    ->options(['left'=>'Izquierda','center'=>'Centro','right'=>'Derecha','justify'=>'Justificado'])->default('left'),
                Forms\Components\Select::make('content.max_width')->label('Ancho máximo')
                    ->options(['narrow'=>'Estrecho (600px)','normal'=>'Normal (760px)','wide'=>'Ancho (1000px)','full'=>'Full'])->default('normal'),
                Forms\Components\TextInput::make('content.font_size')->label('Tamaño fuente (rem)')->default('1')->suffix('rem'),
                Forms\Components\TextInput::make('content.line_height')->label('Altura de línea')->default('1.75'),
                Forms\Components\ColorPicker::make('content.text_color')->label('Color texto'),
            ],
            'image' => [
                Forms\Components\TextInput::make('content.src')->label('URL de la imagen')->required(),
                Forms\Components\TextInput::make('content.alt')->label('Texto alternativo (SEO)'),
                Forms\Components\TextInput::make('content.caption')->label('Pie de foto'),
                Forms\Components\Select::make('content.align')->label('Alineación')
                    ->options(['left'=>'Izquierda','center'=>'Centro','right'=>'Derecha'])->default('center'),
                Forms\Components\TextInput::make('content.max_width')->label('Ancho máximo')->placeholder('800px o 100%')->default('100%'),
                Forms\Components\TextInput::make('content.border_radius')->label('Bordes redondeados (px)')->numeric()->default(0)->suffix('px'),
                Forms\Components\Toggle::make('content.shadow')->label('Sombra'),
                Forms\Components\TextInput::make('content.link_url')->label('Enlace al hacer clic'),
                Forms\Components\Toggle::make('content.link_new_tab')->label('Abrir en nueva pestaña'),
            ],
            'spacer' => [
                Forms\Components\TextInput::make('content.height')->label('Altura (px)')->numeric()->default(60)->suffix('px')->required(),
            ],
            'divider' => [
                Forms\Components\Select::make('content.style')->label('Estilo')
                    ->options(['solid'=>'Línea sólida','dashed'=>'Discontinuo','dotted'=>'Punteado','dots'=>'Puntos ···','asterisk'=>'Asteriscos ✦'])
                    ->default('solid'),
                Forms\Components\ColorPicker::make('content.color')->label('Color')->default('#e0dbd5'),
                Forms\Components\TextInput::make('content.thickness')->label('Grosor (px)')->numeric()->default(1)->suffix('px'),
                Forms\Components\TextInput::make('content.width')->label('Ancho')->placeholder('100% o 600px')->default('100%'),
                Forms\Components\TextInput::make('content.padding')->label('Espacio vertical (px)')->numeric()->default(20)->suffix('px'),
            ],
            'columns' => [
                Forms\Components\Select::make('content.vertical_align')->label('Alineación vertical')
                    ->options(['top'=>'Arriba','center'=>'Centro','bottom'=>'Abajo'])->default('top'),
                Forms\Components\TextInput::make('content.gap')->label('Espacio entre columnas')->default('2rem'),
                Forms\Components\Repeater::make('content.columns')
                    ->label('Columnas')
                    ->schema([
                        Forms\Components\TextInput::make('title')->label('Título'),
                        Forms\Components\Textarea::make('body')->label('Texto')->rows(4),
                        Forms\Components\TextInput::make('image')->label('Imagen URL'),
                        Forms\Components\TextInput::make('image_alt')->label('Alt imagen'),
                        Forms\Components\TextInput::make('button_text')->label('Botón texto'),
                        Forms\Components\TextInput::make('button_url')->label('Botón URL'),
                        Forms\Components\Select::make('text_align')->label('Alineación')
                            ->options(['left'=>'Izquierda','center'=>'Centro','right'=>'Derecha'])->default('left'),
                        Forms\Components\ColorPicker::make('bg_color')->label('Fondo columna'),
                        Forms\Components\TextInput::make('border_radius')->label('Radio bordes (px)')->numeric()->default(0),
                        Forms\Components\TextInput::make('padding')->label('Padding interno')->placeholder('1.5rem'),
                    ])
                    ->columns(2)
                    ->minItems(2)
                    ->maxItems(4)
                    ->defaultItems(2),
            ],
            default => [
                Forms\Components\KeyValue::make('content')->label('Contenido')->reorderable(),
            ],
        };
    }
}
