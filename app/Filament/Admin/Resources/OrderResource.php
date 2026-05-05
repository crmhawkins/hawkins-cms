<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\SiteSettings;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Pedidos';
    protected static ?string $modelLabel = 'Pedido';
    protected static ?string $pluralModelLabel = 'Pedidos';
    protected static ?int $navigationSort = 20;

    public static function shouldRegisterNavigation(): bool
    {
        return (bool) SiteSettings::instance()->ecommerce_enabled;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Información del pedido')->schema([
                Infolists\Components\TextEntry::make('order_number')->label('Nº Pedido')->copyable(),
                Infolists\Components\TextEntry::make('status')->label('Estado')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid'      => 'success',
                        'pending'   => 'warning',
                        'refunded'  => 'danger',
                        'shipped'   => 'info',
                        default     => 'gray',
                    }),
                Infolists\Components\TextEntry::make('created_at')->label('Fecha')->dateTime('d/m/Y H:i'),
                Infolists\Components\TextEntry::make('paid_at')->label('Pagado el')->dateTime('d/m/Y H:i')->placeholder('—'),
                Infolists\Components\TextEntry::make('shipped_at')->label('Enviado el')->dateTime('d/m/Y H:i')->placeholder('—'),
            ])->columns(3),

            Infolists\Components\Section::make('Cliente')->schema([
                Infolists\Components\TextEntry::make('customer_name')->label('Nombre'),
                Infolists\Components\TextEntry::make('customer_email')->label('Email')->copyable(),
                Infolists\Components\TextEntry::make('customer_phone')->label('Teléfono')->placeholder('—'),
            ])->columns(3),

            Infolists\Components\Section::make('Artículos')->schema([
                Infolists\Components\RepeatableEntry::make('items')->label('')->schema([
                    Infolists\Components\TextEntry::make('name')->label('Producto'),
                    Infolists\Components\TextEntry::make('qty')->label('Cantidad'),
                    Infolists\Components\TextEntry::make('price')->label('Precio unit.')
                        ->formatStateUsing(fn ($state) => number_format($state / 100, 2, ',', '.') . ' €'),
                ])->columns(3),
            ]),

            Infolists\Components\Section::make('Totales')->schema([
                Infolists\Components\TextEntry::make('subtotal')->label('Subtotal')
                    ->formatStateUsing(fn ($state) => number_format($state / 100, 2, ',', '.') . ' €'),
                Infolists\Components\TextEntry::make('tax_amount')->label('IVA')
                    ->formatStateUsing(fn ($state) => number_format($state / 100, 2, ',', '.') . ' €'),
                Infolists\Components\TextEntry::make('total')->label('Total')
                    ->formatStateUsing(fn ($state) => number_format($state / 100, 2, ',', '.') . ' €')
                    ->weight(\Filament\Support\Enums\FontWeight::Bold),
            ])->columns(3),

            Infolists\Components\Section::make('Pago')->schema([
                Infolists\Components\TextEntry::make('payment_gateway')->label('Pasarela'),
                Infolists\Components\TextEntry::make('payment_id')->label('ID sesión pago')->placeholder('—')->copyable(),
                Infolists\Components\TextEntry::make('stripe_payment_intent_id')->label('Payment Intent')->placeholder('—')->copyable(),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->label('Nº Pedido')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('customer_name')->label('Cliente')->searchable(),
                Tables\Columns\TextColumn::make('customer_email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('status')->label('Estado')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid'     => 'success',
                        'pending'  => 'warning',
                        'refunded' => 'danger',
                        'shipped'  => 'info',
                        default    => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total')->label('Total')
                    ->formatStateUsing(fn ($state) => number_format($state / 100, 2, ',', '.') . ' €')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('ship')
                    ->label('Marcar enviado')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->action(function (Order $record) {
                        $record->update(['status' => 'shipped', 'shipped_at' => now()]);
                        Notification::make()->title('Pedido marcado como enviado')->success()->send();
                    })
                    ->requiresConfirmation()
                    ->hidden(fn (Order $r) => in_array($r->status, ['shipped', 'refunded'])),
                Tables\Actions\Action::make('refund')
                    ->label('Reembolsar')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('amount_euros')
                            ->label('Importe a reembolsar (€)')
                            ->numeric()
                            ->required()
                            ->helperText('Dejar 0 para reembolso total'),
                    ])
                    ->action(function (Order $record, array $data) {
                        try {
                            $amountCents = (int) round(($data['amount_euros'] ?? 0) * 100);
                            app(\App\Services\Payments\PaymentGatewayFactory::class)::make()->refund($record, $amountCents);
                            Notification::make()->title('Reembolso procesado')->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title('Error: ' . $e->getMessage())->danger()->send();
                        }
                    })
                    ->hidden(fn (Order $r) => $r->status !== 'paid'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('markShipped')
                        ->label('Marcar como enviados')
                        ->icon('heroicon-o-truck')
                        ->action(fn ($records) => $records->each(fn (Order $r) => $r->update(['status' => 'shipped', 'shipped_at' => now()])))
                        ->requiresConfirmation(),
                ]),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view'  => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
