<?php
namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FooterResource\Pages;
use App\Models\Footer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FooterResource extends Resource
{
    protected static ?string $model = Footer::class;
    protected static ?string $navigationIcon = 'heroicon-o-bars-3-bottom-left';
    protected static ?string $navigationLabel = 'Pies de página';
    protected static ?string $navigationGroup = 'Diseño';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Pie de página';
    protected static ?string $pluralModelLabel = 'Pies de página';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identificación')
                ->schema([
                    Forms\Components\TextInput::make('name')->label('Nombre interno')->required()->maxLength(100),
                    Forms\Components\Select::make('type')->label('Tipo de footer')
                        ->options([
                            'classic'  => 'Clásico — 4 columnas',
                            'centered' => 'Centrado — Logo + enlaces centrados',
                            'dark'     => 'Oscuro — Fondo oscuro con newsletter',
                            'minimal'  => 'Mínimo — Solo copyright',
                            'mega'     => 'Mega — Newsletter + columnas + barra inferior',
                        ])
                        ->required()->default('classic'),
                    Forms\Components\Toggle::make('is_default')->label('Footer predeterminado del sitio'),
                ])->columns(3),

            Forms\Components\Section::make('Logo y Branding')
                ->schema([
                    Forms\Components\FileUpload::make('logo_path')->label('Logo')->image()->directory('footers')->imagePreviewHeight('50'),
                    Forms\Components\TextInput::make('logo_text')->label('Texto del logo (fallback)'),
                    Forms\Components\Textarea::make('tagline')->label('Eslogan / Descripción corta')->rows(2),
                ])->columns(3),

            Forms\Components\Section::make('Colores')
                ->schema([
                    Forms\Components\ColorPicker::make('bg_color')->label('Fondo')->default('#111111'),
                    Forms\Components\ColorPicker::make('text_color')->label('Texto')->default('#ffffff'),
                    Forms\Components\ColorPicker::make('link_color')->label('Color links')->default('#c9a96e'),
                    Forms\Components\ColorPicker::make('border_color')->label('Color separadores')->default('#333333'),
                ])->columns(4),

            Forms\Components\Section::make('Información de contacto')
                ->schema([
                    Forms\Components\TextInput::make('phone')->label('Teléfono')->tel(),
                    Forms\Components\TextInput::make('email')->label('Email')->email(),
                    Forms\Components\TextInput::make('address')->label('Dirección'),
                ])->columns(3),

            Forms\Components\Section::make('Redes sociales')
                ->schema([
                    Forms\Components\TextInput::make('social_instagram')->label('Instagram')->url(),
                    Forms\Components\TextInput::make('social_facebook')->label('Facebook')->url(),
                    Forms\Components\TextInput::make('social_twitter')->label('Twitter/X')->url(),
                    Forms\Components\TextInput::make('social_linkedin')->label('LinkedIn')->url(),
                    Forms\Components\TextInput::make('social_youtube')->label('YouTube')->url(),
                ])->columns(2)->collapsible()->collapsed(),

            Forms\Components\Section::make('Boletín de noticias')
                ->schema([
                    Forms\Components\Toggle::make('show_newsletter')->label('Mostrar sección boletín')->live(),
                    Forms\Components\TextInput::make('newsletter_title')->label('Título del boletín'),
                    Forms\Components\TextInput::make('newsletter_placeholder')->label('Texto de ejemplo del email')->default('Tu email'),
                ])->columns(3),

            Forms\Components\Section::make('Copyright')
                ->schema([
                    Forms\Components\TextInput::make('copyright_text')
                        ->label('Texto copyright')
                        ->placeholder('© 2025 Mi Empresa. Todos los derechos reservados.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nombre')->searchable(),
                Tables\Columns\BadgeColumn::make('type')->label('Tipo')
                    ->colors(['primary'=>'classic','success'=>'centered','gray'=>'dark','warning'=>'minimal','danger'=>'mega']),
                Tables\Columns\IconColumn::make('is_default')->label('Predeterminado')->boolean(),
                Tables\Columns\IconColumn::make('show_newsletter')->label('Boletín')->boolean(),
                Tables\Columns\ColorColumn::make('bg_color')->label('Fondo'),
                Tables\Columns\TextColumn::make('updated_at')->label('Actualizado')->since()->sortable(),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFooters::route('/'),
            'create' => Pages\CreateFooter::route('/create'),
            'edit'   => Pages\EditFooter::route('/{record}/edit'),
        ];
    }
}
