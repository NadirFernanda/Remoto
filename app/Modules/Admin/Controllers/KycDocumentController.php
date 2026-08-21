<?php

namespace App\Modules\Admin\Controllers;

use App\Models\KycSubmission;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class KycDocumentController extends Controller
{
    /**
     * Serve um documento de identidade submetido para KYC. Nunca através de
     * Storage::disk('private')->url() — essa rota fica em /storage/{path},
     * o mesmo prefixo já usado pela ligação simbólica pública
     * (public/storage), por isso o nginx intercepta-a como ficheiro estático
     * antes de chegar ao Laravel e devolve sempre 404. Serve-se aqui de
     * forma explícita, autenticada (mesmo grupo de middleware que o resto
     * de Admin > Utilizadores) e sem confiar em nenhum caminho vindo do
     * pedido — só os três campos já validados da submissão.
     */
    public function show(KycSubmission $submission, string $type)
    {
        $path = match ($type) {
            'front'  => $submission->document_front_path,
            'back'   => $submission->document_back_path,
            'selfie' => $submission->selfie_path,
            default  => null,
        };

        abort_if(!$path, 404);
        abort_unless(Storage::disk('private')->exists($path), 404);

        return Storage::disk('private')->response($path);
    }
}
