<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class MediaLibraryController extends Controller
{
    public function index()
    {
        $media = Media::paginate(20);
        return view('media.index', compact('media'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,gif,mp4,pdf|max:10240',
        ]);

        $file = $request->file('file');
        $path = $file->store('media', 'public');

        if (in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif'])) {
            // Generate thumbnails
            $image = Image::make(storage_path("app/public/{$path}"));
            $image->fit(300, 300)->save(
                storage_path("app/public/thumbnails/{$path}")
            );
        }

        Media::create([
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        return response()->json([
            'message' => 'File uploaded successfully',
        ]);
    }
}