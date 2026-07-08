<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class ExtensionController extends Controller
{
    private const SOURCE_DIR = 'browser-extension';

    public function show(Request $request)
    {
        return view('extension.show', [
            'isIos' => (bool) preg_match('/iPhone|iPad|iPod/i', $request->userAgent() ?? ''),
        ]);
    }

    /** Compacta browser-extension/ num .zip e serve para download (sempre actualizado com o código-fonte). */
    public function download(): BinaryFileResponse
    {
        $sourceDir = base_path(self::SOURCE_DIR);
        $zipPath = storage_path('app/site-freelancer-extensao.zip');

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach (File::allFiles($sourceDir) as $file) {
            $relativePath = 'site-freelancer-extensao/' . $file->getRelativePathname();
            $zip->addFile($file->getPathname(), str_replace('\\', '/', $relativePath));
        }

        $zip->close();

        return response()->download($zipPath, 'site-freelancer-extensao.zip')->deleteFileAfterSend(false);
    }
}
