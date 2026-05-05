<?php
namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\Block;
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
            'block_id' => ['required', 'integer'],
            'path'     => ['required', 'string'],
        ]);

        $block = Block::findOrFail($request->block_id);
        $this->authorize('update', $block);

        $file = $request->file('image');
        $tenantId = function_exists('tenant') && tenant() ? tenant('id') : 'central';
        $directory = "tenants/{$tenantId}/images";
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs($directory, $filename, 'public');
        $url = Storage::url($path);

        // Update block content
        $content = $block->content ?? [];
        data_set($content, $request->path, $url);
        $block->content = $content;
        $block->save();

        $block->revisions()->create([
            'content' => $content,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['ok' => true, 'url' => $url, 'path' => $path]);
    }

    public function destroy(int $id): JsonResponse
    {
        // Future: media library cleanup
        return response()->json(['ok' => true]);
    }
}
