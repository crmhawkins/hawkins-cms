<?php

namespace App\Filament\Admin\Pages;

use App\Models\Media;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class MediaLibraryPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Biblioteca de medios';
    protected static ?string $navigationGroup = 'Contenido';
    protected static ?int $navigationSort = 20;
    protected static string $view = 'filament.admin.pages.media-library';

    public string $search = '';

    public function getMediaProperty()
    {
        return Media::when($this->search, fn ($q) => $q->where('original_name', 'like', '%' . $this->search . '%'))
            ->latest()
            ->paginate(24);
    }

    public function deleteMedia(int $id): void
    {
        $media = Media::findOrFail($id);
        Storage::disk($media->disk)->delete($media->directory . '/' . $media->filename);
        $media->delete();

        Notification::make()->title('Archivo eliminado')->success()->send();
    }
}
