<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PostResource\Pages;
use App\Models\Category;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Contenido';
    protected static ?string $navigationLabel = 'Posts';
    protected static ?string $modelLabel = 'Post';
    protected static ?string $pluralModelLabel = 'Posts';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Contenido')->schema([
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
                    ->unique(Post::class, 'slug', ignoreRecord: true),

                Forms\Components\Select::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name')
                    ->nullable()
                    ->searchable()
                    ->preload(),

                Forms\Components\Textarea::make('excerpt')
                    ->label('Extracto')
                    ->nullable()
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\RichEditor::make('body')
                    ->label('Contenido')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('featured_image')
                    ->label('Imagen destacada')
                    ->image()
                    ->disk('public')
                    ->directory('posts')
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Publicación')->schema([
                Forms\Components\Select::make('status')
                    ->label('Estado')
                    ->options(['draft' => 'Borrador', 'published' => 'Publicado'])
                    ->default('draft')
                    ->required(),

                Forms\Components\DateTimePicker::make('published_at')
                    ->label('Publicar el')
                    ->nullable(),
            ])->columns(2),

            Forms\Components\Section::make('SEO')->schema([
                Forms\Components\TextInput::make('meta_title')
                    ->label('Meta título')
                    ->maxLength(70)
                    ->placeholder('Título para buscadores (max 70 caracteres)'),

                Forms\Components\Textarea::make('meta_description')
                    ->label('Meta descripción')
                    ->maxLength(320)
                    ->rows(2)
                    ->placeholder('Descripción para buscadores (max 160 caracteres)'),

                Forms\Components\TextInput::make('og_image')
                    ->label('Imagen OG (URL o ruta)')
                    ->maxLength(500)
                    ->placeholder('/storage/og/post.jpg'),

                Forms\Components\TextInput::make('meta_robots')
                    ->label('Robots')
                    ->default('index,follow')
                    ->maxLength(100),
            ])->columns(2)->collapsible()->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoría')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft'     => 'warning',
                        default     => 'gray',
                    }),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publicado el')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('—'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publicar seleccionados')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update(['status' => 'published']))
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('unpublish')
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
            'index'  => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit'   => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
