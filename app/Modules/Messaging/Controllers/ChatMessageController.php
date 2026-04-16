<?php

namespace App\Modules\Messaging\Controllers;

use App\Models\Message;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatMessageController extends Controller
{
    /** PATCH /chat/mensagem/{message} — editar conteúdo */
    public function update(Request $request, Message $message)
    {
        abort_unless(Auth::check(), 401);
        abort_unless($message->user_id === Auth::id(), 403);
        abort_if($message->deleted_at !== null, 404);

        $request->validate([
            'conteudo' => 'required|string|max:5000',
        ]);

        $message->update([
            'conteudo'  => trim($request->input('conteudo')),
            'edited_at' => now(),
        ]);

        return response()->json([
            'id'        => $message->id,
            'conteudo'  => $message->conteudo,
            'edited_at' => $message->edited_at->format('H:i'),
        ]);
    }

    /** DELETE /chat/mensagem/{message} — eliminar mensagem */
    public function destroy(Message $message)
    {
        abort_unless(Auth::check(), 401);
        abort_unless($message->user_id === Auth::id(), 403);

        // Apagar ficheiro anexado se existir
        if ($message->anexo) {
            Storage::disk('public')->delete('anexos/' . $message->anexo);
        }

        $message->delete(); // soft delete

        return response()->json(['ok' => true, 'id' => $message->id]);
    }
}
