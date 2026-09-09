<?php

namespace App\Modules\Marketplace\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PortfolioMediaController extends Controller
{
    public function show(Portfolio $portfolio): Response
    {
        abort_unless($portfolio->is_public && $portfolio->media_path, 404);

        $path = ltrim($portfolio->media_path, '/');
        $path = Str::startsWith($path, 'storage/')
            ? Str::after($path, 'storage/')
            : $path;

        abort_unless(Storage::disk('public')->exists($path), 404);

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION)) ?: 'bin';
        $filename = Str::slug($portfolio->title ?: 'documento-portfolio') . '.' . $extension;
        $mimeType = Storage::disk('public')->mimeType($path) ?: 'application/octet-stream';

        return response(Storage::disk('public')->get($path), 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
