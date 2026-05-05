<?php

namespace App\Filament\Admin\Pages;

use App\Models\SiteSettings;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class SiteSettingsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Ajustes del sitio';
    protected static ?string $title = 'Ajustes del sitio';
    protected static ?int $navigationSort = 99;
    protected static string $view = 'filament.admin.pages.site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = SiteSettings::instance();
        $this->form->fill($settings->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('site_name')->label('Nombre del sitio')->required(),
                TextInput::make('site_url')->label('URL del sitio')->url()->required(),
                Select::make('theme')->label('Tema')->options(['default' => 'Default', 'sanzahra' => 'Sanzahra'])->default('default'),
                Toggle::make('ecommerce_enabled')->label('E-commerce activo'),
                Select::make('payment_gateway')->label('Pasarela de pago')->options([
                    'none' => 'Ninguna',
                    'stripe_connect' => 'Stripe Connect',
                    'redsys' => 'Redsys',
                    'paypal' => 'PayPal',
                ])->default('none'),
                TextInput::make('stripe_secret_key')->label('Stripe Secret Key')->password()->dehydrateStateUsing(fn ($state) => filled($state) ? $state : SiteSettings::instance()->stripe_secret_key),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')->label('Guardar')->action('save'),
        ];
    }

    public function save(): void
    {
        $settings = SiteSettings::instance();
        $settings->update($this->form->getState());
        Notification::make()->title('Ajustes guardados')->success()->send();
    }
}
