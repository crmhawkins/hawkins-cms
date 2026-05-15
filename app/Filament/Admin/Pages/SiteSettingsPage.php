<?php

namespace App\Filament\Admin\Pages;

use App\Models\SiteSettings;
use Filament\Forms;
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
        return $form->schema([
            Forms\Components\Tabs::make('Ajustes')->tabs([

                Forms\Components\Tabs\Tab::make('General')->schema([
                    Forms\Components\TextInput::make('site_name')->label('Nombre del sitio')->required(),
                    Forms\Components\TextInput::make('site_url')->label('URL del sitio')->url()->required(),
                    Forms\Components\Select::make('theme')->label('Tema activo')
                        ->options(['sanzahra' => 'Sanzahra'])->default('sanzahra'),
                    Forms\Components\FileUpload::make('logo_path')->label('Logo del sitio')->image()->directory('site')->imagePreviewHeight('50'),
                    Forms\Components\FileUpload::make('favicon_path')->label('Favicon')->image()->directory('site')->imagePreviewHeight('32')->helperText('Imagen cuadrada 32x32 o 64x64 px'),
                    Forms\Components\TextInput::make('contact_email')->label('Email de contacto')->email(),
                ])->columns(2),

                Forms\Components\Tabs\Tab::make('Diseño')->schema([
                    Forms\Components\ColorPicker::make('accent_color')->label('Color de acento')->default('#c9a96e')->helperText('Color principal del tema (botones, hovers, etc.)'),
                    Forms\Components\Select::make('font_heading')->label('Fuente títulos')
                        ->options([
                            'Cormorant Garamond' => 'Cormorant Garamond (serif elegante)',
                            'Playfair Display'   => 'Playfair Display (serif clásico)',
                            'Lora'               => 'Lora (serif moderno)',
                            'Raleway'            => 'Raleway (sans-serif fino)',
                            'Poppins'            => 'Poppins (sans-serif redondo)',
                            'Montserrat'         => 'Montserrat (sans-serif geométrico)',
                        ])->default('Cormorant Garamond'),
                    Forms\Components\Select::make('font_body')->label('Fuente cuerpo')
                        ->options([
                            'Montserrat' => 'Montserrat',
                            'Open Sans'  => 'Open Sans',
                            'Raleway'    => 'Raleway',
                            'Lato'       => 'Lato',
                            'Inter'      => 'Inter',
                            'Nunito'     => 'Nunito',
                        ])->default('Montserrat'),
                    Forms\Components\Select::make('default_header_id')->label('Header predeterminado del sitio')
                        ->options(fn () => \App\Models\Header::pluck('name', 'id')->toArray())
                        ->placeholder('— Sin header predeterminado —')
                        ->searchable(),
                    Forms\Components\Select::make('default_footer_id')->label('Footer predeterminado del sitio')
                        ->options(fn () => \App\Models\Footer::pluck('name', 'id')->toArray())
                        ->placeholder('— Sin footer predeterminado —')
                        ->searchable(),
                ])->columns(2),

                Forms\Components\Tabs\Tab::make('Redes sociales')->schema([
                    Forms\Components\TextInput::make('social_instagram')->label('Instagram')->url()->prefix('https://'),
                    Forms\Components\TextInput::make('social_facebook')->label('Facebook')->url()->prefix('https://'),
                    Forms\Components\TextInput::make('social_twitter')->label('Twitter / X')->url()->prefix('https://'),
                    Forms\Components\TextInput::make('social_linkedin')->label('LinkedIn')->url()->prefix('https://'),
                    Forms\Components\TextInput::make('social_youtube')->label('YouTube')->url()->prefix('https://'),
                ])->columns(2),

                Forms\Components\Tabs\Tab::make('E-commerce')->schema([
                    Forms\Components\Toggle::make('ecommerce_enabled')->label('E-commerce activo'),
                    Forms\Components\Select::make('payment_gateway')->label('Pasarela de pago')
                        ->options(['none'=>'Ninguna','stripe_connect'=>'Stripe Connect','redsys'=>'Redsys','paypal'=>'PayPal'])
                        ->default('none'),
                    Forms\Components\TextInput::make('stripe_secret_key')->label('Stripe Secret Key')->password()
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? $state : \App\Models\SiteSettings::instance()->stripe_secret_key),
                    Forms\Components\TextInput::make('stripe_webhook_secret')->label('Stripe Webhook Secret')->password()
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? $state : \App\Models\SiteSettings::instance()->stripe_webhook_secret),
                ])->columns(2),

                Forms\Components\Tabs\Tab::make('Analytics / Código')->schema([
                    Forms\Components\Textarea::make('google_analytics_code')
                        ->label('Código Google Analytics / GTM')
                        ->rows(4)
                        ->placeholder('<!-- Global site tag (gtag.js) -->')
                        ->helperText('Se inserta en el <head> de todas las páginas'),
                    Forms\Components\Textarea::make('custom_head_code')
                        ->label('Código personalizado <head>')
                        ->rows(4)
                        ->helperText('Scripts, meta tags, etc. globales'),
                    Forms\Components\Textarea::make('custom_body_code')
                        ->label('Código personalizado <body>')
                        ->rows(4)
                        ->helperText('Scripts antes de </body>'),
                ]),

                Forms\Components\Tabs\Tab::make('Mantenimiento')->schema([
                    Forms\Components\Toggle::make('maintenance_mode')
                        ->label('Modo mantenimiento')
                        ->helperText('Actívalo para mostrar una página de mantenimiento a los visitantes (el admin sigue accesible)')
                        ->live(),
                    Forms\Components\Textarea::make('maintenance_message')
                        ->label('Mensaje de mantenimiento')
                        ->rows(3)
                        ->default('Sitio en mantenimiento. Volvemos pronto.')
                        ->visible(fn (Forms\Get $get) => $get('maintenance_mode')),
                ]),

            ])->columnSpanFull(),
        ])->statePath('data');
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
