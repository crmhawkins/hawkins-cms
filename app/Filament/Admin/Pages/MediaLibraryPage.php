<?php
namespace App\Filament\Admin\Pages;

use App\Models\Media;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
    public string $editingName = '';

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

    public function getEditingFileProperty(): ?Media
    {
        return $this->editingMediaId ? Media::find($this->editingMediaId) : null;
    }

    public function saveUploads(): void
    {
        $this->validate(['uploads.*' => 'file|max:51200']);

        $count = count($this->uploads);
        foreach ($this->uploads as $file) {
            $ext      = $file->getClientOriginalExtension() ?: $file->extension();
            $filename = Str::uuid() . '.' . $ext;
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
        }

        $this->uploads = [];
        Notification::make()
            ->title($count === 1 ? 'Archivo subido' : "{$count} archivos subidos")
            ->success()->send();
    }

    public function startEdit(int $id): void
    {
        $media = Media::find($id);
        if (!$media) return;
        $this->editingMediaId = $id;
        $this->editingAlt     = $media->alt ?? '';
        $this->editingName    = $media->original_name;
    }

    public function saveEdit(): void
    {
        if (!$this->editingMediaId) return;
        $media = Media::find($this->editingMediaId);
        if (!$media) return;

        $media->update([
            'alt'           => $this->editingAlt,
            'original_name' => trim($this->editingName) ?: $media->original_name,
        ]);

        Notification::make()->title('Cambios guardados')->success()->send();
        $this->editingMediaId = null;
    }

    public function saveCrop(int $x, int $y, int $width, int $height): void
    {
        $media = Media::findOrFail($this->editingMediaId);

        if (!$media->isImage()) {
            Notification::make()->title('Solo se pueden recortar imágenes')->warning()->send();
            return;
        }

        $sourcePath = Storage::disk($media->disk)->path($media->directory . '/' . $media->filename);
        if (!file_exists($sourcePath)) {
            Notification::make()->title('Archivo no encontrado en disco')->danger()->send();
            return;
        }

        $img = @imagecreatefromstring(file_get_contents($sourcePath));
        if (!$img) {
            Notification::make()->title('No se pudo leer la imagen')->danger()->send();
            return;
        }

        $width  = max(1, min($width, imagesx($img)));
        $height = max(1, min($height, imagesy($img)));
        $x      = max(0, $x);
        $y      = max(0, $y);

        $cropped = imagecreatetruecolor($width, $height);
        imagecopy($cropped, $img, 0, 0, $x, $y, $width, $height);
        imagedestroy($img);

        ob_start();
        imagejpeg($cropped, null, 90);
        $data = ob_get_clean();
        imagedestroy($cropped);

        $newFilename = Str::uuid() . '.jpg';
        Storage::disk($media->disk)->put($media->directory . '/' . $newFilename, $data);

        $nameBase = pathinfo($media->original_name, PATHINFO_FILENAME);
        Media::create([
            'disk'          => $media->disk,
            'directory'     => $media->directory,
            'filename'      => $newFilename,
            'original_name' => $nameBase . '-recortado.jpg',
            'mime_type'     => 'image/jpeg',
            'size'          => strlen($data),
            'alt'           => $media->alt,
        ]);

        Notification::make()->title('Imagen recortada y duplicada')->success()->send();
        $this->editingMediaId = null;
    }

    public function deleteMedia(int $id): void
    {
        $media = Media::findOrFail($id);
        Storage::disk($media->disk)->delete($media->directory . '/' . $media->filename);
        $media->delete();
        $this->editingMediaId = null;
        Notification::make()->title('Archivo eliminado')->success()->send();
    }

    public function deleteSelected(array $ids): void
    {
        if (empty($ids)) return;
        $medias = Media::whereIn('id', $ids)->get();
        foreach ($medias as $media) {
            Storage::disk($media->disk)->delete($media->directory . '/' . $media->filename);
            $media->delete();
        }
        $count = $medias->count();
        Notification::make()->title("{$count} archivo(s) eliminados")->success()->send();
    }
}
