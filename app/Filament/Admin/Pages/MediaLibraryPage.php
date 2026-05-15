<?php
namespace App\Filament\Admin\Pages;

use App\Models\Media;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class MediaLibraryPage extends Page
{
    use WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Biblioteca de medios';
    protected static ?string $navigationGroup = 'Contenido';
    protected static ?int $navigationSort = 20;
    protected static string $view = 'filament.admin.pages.media-library';

    public string $search = '';
    public string $filterType = '';
    public array $uploads = [];
    public ?int $editingMediaId = null;
    public string $editingAlt = '';

    public function getMediaProperty()
    {
        return Media::query()
            ->when($this->search, fn ($q) => $q->where('original_name', 'like', '%'.$this->search.'%'))
            ->when($this->filterType === 'image', fn ($q) => $q->where('mime_type', 'like', 'image/%'))
            ->when($this->filterType === 'video', fn ($q) => $q->where('mime_type', 'like', 'video/%'))
            ->when($this->filterType === 'document', fn ($q) => $q->whereNotIn('mime_type', [])
                ->where(fn ($q) => $q->where('mime_type', 'like', 'application/%')->orWhere('mime_type', 'like', 'text/%')))
            ->latest()
            ->paginate(24);
    }

    public function saveUploads(): void
    {
        $this->validate(['uploads.*' => 'file|max:51200']);

        $count = 0;
        foreach ($this->uploads as $file) {
            $ext      = $file->getClientOriginalExtension() ?: $file->extension();
            $filename = \Illuminate\Support\Str::uuid() . '.' . $ext;
            $file->storeAs('images', $filename, 'public');

            Media::create([
                'disk'          => 'public',
                'directory'     => 'images',
                'filename'      => $filename,
                'original_name' => $file->getClientOriginalName(),
                'mime_type'     => $file->getMimeType(),
                'size'          => $file->getSize(),
                'alt'           => '',
            ]);
            $count++;
        }

        $this->uploads = [];
        Notification::make()
            ->title($count === 1 ? 'Archivo subido' : "{$count} archivos subidos")
            ->success()->send();
    }

    public function startEditAlt(int $id): void
    {
        $this->editingMediaId = $id;
        $this->editingAlt     = Media::find($id)?->alt ?? '';
    }

    public function saveAlt(): void
    {
        if ($this->editingMediaId) {
            Media::find($this->editingMediaId)?->update(['alt' => $this->editingAlt]);
            $this->editingMediaId = null;
            $this->editingAlt     = '';
            Notification::make()->title('Alt text guardado')->success()->send();
        }
    }

    public function deleteMedia(int $id): void
    {
        $media = Media::findOrFail($id);
        Storage::disk($media->disk)->delete($media->directory.'/'.$media->filename);
        $media->delete();
        Notification::make()->title('Archivo eliminado')->success()->send();
    }
}
