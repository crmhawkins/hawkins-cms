<?php
namespace App\Filament\Admin\Resources\HeaderResource\Pages;
use App\Filament\Admin\Resources\HeaderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditHeader extends EditRecord {
    protected static string $resource = HeaderResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
}
