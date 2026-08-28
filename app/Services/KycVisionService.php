<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class KycVisionService
{
    /**
     * Tenta extrair o nome completo, o número do BI e o texto bruto da
     * frente do documento usando a Google Cloud Vision API. Nunca lança
     * excepção — qualquer falha (chave em falta, rede, documento ilegível)
     * resulta em valores a null, para nunca bloquear a revisão do KYC pelo
     * admin. O resultado fica em cache por submissão, para reabrir o mesmo
     * pedido não gastar uma nova chamada à API.
     *
     * @return array{name: ?string, document_number: ?string, raw_text: ?string}
     */
    public function extractFromDocument(int $submissionId, string $storagePath): array
    {
        $apiKey = config('services.google_vision.key');
        if (!$apiKey) {
            return ['name' => null, 'document_number' => null, 'raw_text' => null];
        }

        return Cache::remember(
            "kyc_vision_ocr:{$submissionId}",
            now()->addDays(30),
            fn () => $this->callVisionApi($apiKey, $storagePath)
        );
    }

    /**
     * @return array{name: ?string, document_number: ?string, raw_text: ?string}
     */
    private function callVisionApi(string $apiKey, string $storagePath): array
    {
        $empty = ['name' => null, 'document_number' => null, 'raw_text' => null];

        try {
            if (!Storage::disk('private')->exists($storagePath)) {
                return $empty;
            }

            $base64 = base64_encode(Storage::disk('private')->get($storagePath));

            $response = Http::timeout(15)->post(
                'https://vision.googleapis.com/v1/images:annotate?key=' . $apiKey,
                [
                    'requests' => [[
                        'image'        => ['content' => $base64],
                        'features'     => [['type' => 'DOCUMENT_TEXT_DETECTION']],
                        'imageContext' => ['languageHints' => ['pt']],
                    ]],
                ]
            );

            if (!$response->successful()) {
                Log::warning('KycVisionService: chamada à Vision API falhou', ['status' => $response->status()]);
                return $empty;
            }

            $text = $response->json('responses.0.fullTextAnnotation.text');
            if (!$text) {
                return $empty;
            }

            return [
                'name'            => $this->guessNameFromText($text),
                'document_number' => $this->guessDocumentNumberFromText($text),
                'raw_text'        => $text,
            ];
        } catch (\Throwable $e) {
            Log::warning('KycVisionService: erro ao processar documento', ['error' => $e->getMessage()]);
            return $empty;
        }
    }

    /**
     * Heurística best-effort para o BI angolano: procura os rótulos
     * "NOME" e "APELIDO" e o valor junto a eles (mesma linha após ":" ou
     * a linha seguinte). Não é fiável a 100% — é só uma sugestão de
     * preenchimento; o admin confirma sempre visualmente com a foto.
     */
    private function guessNameFromText(string $text): ?string
    {
        $lines = array_values(array_filter(array_map('trim', explode("\n", $text))));

        $nome    = null;
        $apelido = null;

        foreach ($lines as $i => $line) {
            $upper = mb_strtoupper($line);

            if ($apelido === null && preg_match('/\bAPELID[OA]S?\b/u', $upper)) {
                $apelido = $this->valueAfterLabel($line, $lines, $i);
            } elseif ($nome === null && preg_match('/\bNOME(S)?\b/u', $upper)) {
                $nome = $this->valueAfterLabel($line, $lines, $i);
            }
        }

        $full = trim(($nome ?? '') . ' ' . ($apelido ?? ''));

        return $full !== '' ? $full : null;
    }

    private function valueAfterLabel(string $line, array $lines, int $index): ?string
    {
        // O rótulo e o valor tanto podem estar na mesma linha ("NOME: JOÃO")
        // como o valor pode ser a linha seguinte (layout mais comum no BI).
        if (preg_match('/^[^:]*:\s*(.+)$/u', $line, $m) && trim($m[1]) !== '') {
            return trim($m[1]);
        }

        return $lines[$index + 1] ?? null;
    }

    /**
     * O número do BI angolano tem um formato fixo: 9 dígitos + 2 letras +
     * 3 dígitos (ex: "003093887BE035", 14 caracteres). Procuramos esse
     * padrão exacto em qualquer parte do texto extraído — é o campo mais
     * fácil de identificar com segurança, porque o formato é rígido e não
     * depende de rótulos que podem variar/faltar na leitura.
     */
    private function guessDocumentNumberFromText(string $text): ?string
    {
        if (preg_match('/\b(\d{9}[A-Za-z]{2}\d{3})\b/', $text, $m)) {
            return mb_strtoupper($m[1]);
        }

        return null;
    }
}
