<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ContactSubmissionResource\Pages;
use App\Models\ContactSubmission;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactSubmissionResource extends Resource
{
    protected static ?string $model = ContactSubmission::class;
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationLabel = 'Mensajes';
    protected static ?string $modelLabel = 'Mensaje';
    protected static ?string $pluralModelLabel = 'Mensajes';
    protected static ?int $navigationSort = 50;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Asunto')
                    ->placeholder('—')
                    ->limit(40),
                Tables\Columns\TextColumn::make('message')
                    ->label('Mensaje')
                    ->limit(60)
                    ->tooltip(fn (ContactSubmission $r): string => $r->message),
                Tables\Columns\TextColumn::make('read_at')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Leído' : 'No leído')
                    ->color(fn (ContactSubmission $r) => $r->isRead() ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Recibido')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('read')
                    ->label('Marcar leído')
                    ->icon('heroicon-o-check')
                    ->action(fn (ContactSubmission $r) => $r->markAsRead())
                    ->hidden(fn (ContactSubmission $r) => $r->isRead()),
                Tables\Actions\Action::make('reply')
                    ->label('Responder')
                    ->icon('heroicon-o-envelope')
                    ->url(fn (ContactSubmission $r): string => 'mailto:' . $r->email . '?subject=Re: ' . urlencode($r->subject ?? 'Tu mensaje'))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('markRead')
                        ->label('Marcar como leídos')
                        ->action(fn ($records) => $records->each->markAsRead()),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactSubmissions::route('/'),
        ];
    }
}
