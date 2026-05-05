<?php

namespace App\Filament\Admin\Pages;

use App\Models\Header;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class HeaderSettingsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-window';
    protected static ?string $navigationLabel = 'Cabecera del sitio';
    protected static string $view = 'filament.admin.pages.header-settings';
    protected static ?string $title = 'Configuración de cabecera';

    public ?array $data = [];

    public function mount(): void
    {
        $header = Header::getInstance();

        $this->form->fill([
            'layout'     => $header->layout,
            'bg_color'   => $header->bg_color,
            'text_color' => $header->text_color,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('layout')
                    ->label('Diseño')
                    ->options([
                        'split'      => 'Split (links izq + logo + links der)',
                        'logo_left'  => 'Logo a la izquierda',
                        'logo_right' => 'Logo a la derecha',
                    ])
                    ->required(),

                Forms\Components\ColorPicker::make('bg_color')
                    ->label('Color de fondo')
                    ->required(),

                Forms\Components\ColorPicker::make('text_color')
                    ->label('Color de texto')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $header = Header::getInstance();
        $header->update($data);

        Notification::make()
            ->title('Configuración guardada')
            ->success()
            ->send();
    }
}
