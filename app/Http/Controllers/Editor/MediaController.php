<?php
namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'image'    => ['required', 'image', 'max:10240'],
            'block_id' => ['nullable', 'integer'],
            'path'     => ['nullable', 'string'],
        ]);

        $file = $request->file('image');
        $directory = 'images';
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs($directory, $filename, 'public');
        $url = Storage::url($path);

        // Create Media record
        $media = Media::create([
            'disk'          => 'public',
            'directory'     => $directory,
            'filename'      => $filename,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'size'          => $file->getSize(),
        ]);

        // Update block content only when a real block_id is provided
        $blockId = (int) $request->block_id;
        if ($blockId > 0 && $request->path) {
            $block = Block::findOrFail($blockId);
            $this->authorize('update', $block);

            $content = $block->content ?? [];
            data_set($content, $request->path, $url);
            $block->content = $content;
            $block->save();

            $block->revisions()->create([
                'content' => $content,
                'user_id' => auth()->id(),
            ]);
        }

        return response()->json(['ok' => true, 'url' => $url, 'path' => $path, 'media_id' => $media->id]);
    }

    public function destroy(int $id): JsonResponse
    {
        $media = Media::findOrFail($id);
        Storage::disk($media->disk)->delete($media->directory . '/' . $media->filename);
        $media->delete();

        return response()->json(['ok' => true]);
    }
}
