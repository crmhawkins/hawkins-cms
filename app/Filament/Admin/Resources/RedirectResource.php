<?php
namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RedirectResource\Pages;
use App\Models\Redirect;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RedirectResource extends Resource
{
    protected static ?string $model = Redirect::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-circle';
    protected static ?string $navigationLabel = 'Redirecciones';
    protected static ?string $navigationGroup = 'SEO';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('from_url')->label('URL origen')->required()->placeholder('/antigua-url')->helperText('Ruta relativa, ej: /pagina-vieja'),
            Forms\Components\TextInput::make('to_url')->label('URL destino')->required()->placeholder('/nueva-url o https://...'),
            Forms\Components\Select::make('status_code')->label('Tipo')->options(['301'=>'301 — Permanente','302'=>'302 — Temporal'])->default('301')->required(),
            Forms\Components\Toggle::make('active')->label('Activa')->default(true),
            Forms\Components\TextInput::make('notes')->label('Notas')->placeholder('Por qué existe esta redirección'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('from_url')->label('Desde')->searchable(),
                Tables\Columns\TextColumn::make('to_url')->label('Hacia')->searchable(),
                Tables\Columns\BadgeColumn::make('status_code')->label('Código')->colors(['success'=>'301','warning'=>'302']),
                Tables\Columns\IconColumn::make('active')->label('Activa')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->label('Actualizado')->since()->sortable(),
            ])
            ->filters([Tables\Filters\TernaryFilter::make('active')->label('Estado')])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRedirects::route('/'),
            'create' => Pages\CreateRedirect::route('/create'),
            'edit'   => Pages\EditRedirect::route('/{record}/edit'),
        ];
    }
}
