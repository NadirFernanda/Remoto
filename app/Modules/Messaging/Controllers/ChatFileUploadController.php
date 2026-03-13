<?php

namespace App\Modules\Messaging\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ChatFileUploadController extends Controller
{
    public function upload(Request $request)
    {
        abort_unless(auth()->check(), 401);

        $request->validate([
            'file' => [
                'required',
                'file',
                'max:10240', // 10 MB
                'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar',
            ],
        ]);

        $file = $request->file('file');

        $original = $file->getClientOriginalName();
        $safe     = preg_replace('/[^a-zA-Z0-9._-]/', '_', $original);
        $path     = $file->storeAs('anexos', time() . '_' . $safe, 'public');

        if (!$path) {
            return response()->json(['error' => 'Não foi possível guardar o ficheiro.'], 500);
        }

        return response()->json([
            'filename' => basename($path),
            'original' => $original,
        ]);
    }
}
