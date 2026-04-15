<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Serve a file from the public storage disk without needing a symlink.
     */
    public function show(string $path): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
    {
        if (! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $fullPath = storage_path('app/public/'.$path);

        return response()->file($fullPath);
    }
}
