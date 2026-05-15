<?php
namespace App\Filament\Admin\Resources\HeaderResource\Pages;
use App\Filament\Admin\Resources\HeaderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListHeaders extends ListRecords {
    protected static string $resource = HeaderResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
