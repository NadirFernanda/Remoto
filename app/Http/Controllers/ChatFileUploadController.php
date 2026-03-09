<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatFileUploadController extends Controller
{
    public function upload(Request $request)
    {
        abort_unless(auth()->check(), 401);

        $file = $request->file('file');
        if (!$file) {
            return response()->json(['error' => 'Nenhum ficheiro recebido.'], 422);
        }

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
