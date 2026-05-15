<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MediaUploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['file' => 'required|file|max:20480']);

        $file     = $request->file('file');
        $ext      = $file->getClientOriginalExtension() ?: $file->extension();
        $filename = Str::uuid() . '.' . $ext;

        $file->storeAs('images', $filename, 'public');

        $media = Media::create([
            'disk'          => 'public',
            'directory'     => 'images',
            'filename'      => $filename,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'size'          => $file->getSize(),
            'alt'           => '',
        ]);

        return response()->json(['success' => true, 'id' => $media->id]);
    }
}
