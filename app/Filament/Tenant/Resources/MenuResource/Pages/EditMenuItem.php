<?php

namespace App\Filament\Tenant\Resources\MenuResource\Pages;

use App\Filament\Tenant\Resources\MenuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMenuItem extends EditRecord
{
    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
