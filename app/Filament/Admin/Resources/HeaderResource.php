<?php
namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\HeaderResource\Pages;
use App\Models\Header;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HeaderResource extends Resource
{
    protected static ?string $model = Header::class;
    protected static ?string $navigationIcon = 'heroicon-o-window';
    protected static ?string $navigationLabel = 'Cabeceras';
    protected static ?string $navigationGroup = 'Diseño';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Cabecera';
    protected static ?string $pluralModelLabel = 'Cabeceras';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identificación')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nombre interno')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\Select::make('type')
                        ->label('Tipo de header')
                        ->options([
                            'classic'  => 'Clásico — Logo izq, nav derecha',
                            'centered' => 'Centrado — Logo centrado, nav debajo',
                            'split'    => 'Partido — Nav | Logo | Nav',
                            'minimal'  => 'Mínimo — Logo + menú hamburguesa',
                            'mega'     => 'Mega — Barra superior + nav completa',
                        ])
                        ->required()
                        ->default('classic')
                        ->live(),
                    Forms\Components\Toggle::make('is_default')
                        ->label('Header predeterminado del sitio')
                        ->helperText('Solo uno puede ser el predeterminado'),
                ])->columns(3),

            Forms\Components\Section::make('Logo')
                ->schema([
                    Forms\Components\FileUpload::make('logo_path')
                        ->label('Logo (imagen)')
                        ->image()
                        ->directory('headers')
                        ->imagePreviewHeight('60')
                        ->helperText('Si no hay imagen, se usará el texto del logo'),
                    Forms\Components\TextInput::make('logo_text')
                        ->label('Texto del logo')
                        ->helperText('Fallback si no hay imagen'),
                    Forms\Components\TextInput::make('logo_height')
                        ->label('Alto del logo (px)')
                        ->numeric()
                        ->default(50)
                        ->suffix('px'),
                ])->columns(3),

            Forms\Components\Section::make('Colores')
                ->schema([
                    Forms\Components\ColorPicker::make('bg_color')
                        ->label('Fondo')
                        ->default('#ffffff'),
                    Forms\Components\ColorPicker::make('text_color')
                        ->label('Texto / Nav')
                        ->default('#111111'),
                    Forms\Components\ColorPicker::make('hover_color')
                        ->label('Hover links')
                        ->default('#c9a96e'),
                    Forms\Components\ColorPicker::make('active_color')
                        ->label('Link activo')
                        ->default('#c9a96e'),
                ])->columns(4),

            Forms\Components\Section::make('Comportamiento')
                ->schema([
                    Forms\Components\Toggle::make('sticky')
                        ->label('Sticky (se queda fijo al hacer scroll)'),
                    Forms\Components\Toggle::make('transparent_on_top')
                        ->label('Transparente al inicio de página')
                        ->helperText('Útil sobre heroes con imagen'),
                    Forms\Components\Toggle::make('show_search')
                        ->label('Mostrar icono búsqueda'),
                ])->columns(3),

            Forms\Components\Section::make('Botón CTA')
                ->schema([
                    Forms\Components\TextInput::make('cta_text')
                        ->label('Texto del botón'),
                    Forms\Components\TextInput::make('cta_url')
                        ->label('URL del botón')
                        ->url(),
                    Forms\Components\ColorPicker::make('cta_bg_color')
                        ->label('Color fondo botón')
                        ->default('#111111'),
                    Forms\Components\ColorPicker::make('cta_text_color')
                        ->label('Color texto botón')
                        ->default('#ffffff'),
                ])->columns(4),

            Forms\Components\Section::make('Contacto (visible en tipo Mega)')
                ->schema([
                    Forms\Components\TextInput::make('phone')
                        ->label('Teléfono')
                        ->tel(),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email(),
                ])->columns(2),

            Forms\Components\Section::make('Redes sociales')
                ->schema([
                    Forms\Components\Toggle::make('show_social')
                        ->label('Mostrar redes sociales')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('social_instagram')->label('URL de Instagram')->url(),
                    Forms\Components\TextInput::make('social_facebook')->label('URL de Facebook')->url(),
                    Forms\Components\TextInput::make('social_twitter')->label('URL de Twitter/X')->url(),
                    Forms\Components\TextInput::make('social_linkedin')->label('URL de LinkedIn')->url(),
                    Forms\Components\TextInput::make('social_youtube')->label('URL de YouTube')->url(),
                ])->columns(2)->collapsible()->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nombre')->searchable(),
                Tables\Columns\BadgeColumn::make('type')->label('Tipo')
                    ->colors([
                        'primary' => 'classic',
                        'success' => 'centered',
                        'warning' => 'split',
                        'danger'  => 'minimal',
                        'gray'    => 'mega',
                    ]),
                Tables\Columns\IconColumn::make('is_default')->label('Predeterminado')->boolean(),
                Tables\Columns\IconColumn::make('sticky')->label('Sticky')->boolean(),
                Tables\Columns\ColorColumn::make('bg_color')->label('Fondo'),
                Tables\Columns\TextColumn::make('updated_at')->label('Actualizado')->since()->sortable(),
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
            'index'  => Pages\ListHeaders::route('/'),
            'create' => Pages\CreateHeader::route('/create'),
            'edit'   => Pages\EditHeader::route('/{record}/edit'),
        ];
    }
}
