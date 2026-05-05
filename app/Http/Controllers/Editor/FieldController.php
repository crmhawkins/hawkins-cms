<?php
namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\Block;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'block_id' => ['required', 'integer'],
            'path'     => ['required', 'string', 'max:255'],
            'value'    => ['required', 'string', 'max:65535'],
        ]);

        $block = Block::findOrFail($data['block_id']);

        $this->authorize('update', $block);

        $content = $block->content ?? [];
        data_set($content, $data['path'], $data['value']);
        $block->content = $content;
        $block->save();

        // Store revision
        $block->revisions()->create([
            'content' => $content,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['ok' => true, 'updated_at' => $block->updated_at]);
    }
}
