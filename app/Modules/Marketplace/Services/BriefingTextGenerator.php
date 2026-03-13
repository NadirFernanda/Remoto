<?php

namespace App\Modules\Marketplace\Services;

class BriefingTextGenerator
{
    /**
     * Corrige erros ortogrÃ¡ficos simples em portuguÃªs (exemplo bÃ¡sico).
     */
    protected static function corrigirOrtografia($texto)
    {
        // Exemplo simples de correÃ§Ã£o (pode ser expandido ou integrar API externa)
        $correcoes = [
            'proficional' => 'profissional',
            'negosio' => 'negÃ³cio',
            'corre' => 'cores',
            // Adicione mais correÃ§Ãµes conforme necessÃ¡rio
        ];
        return str_ireplace(array_keys($correcoes), array_values($correcoes), $texto);
    }

    /**
     * Remove ou substitui palavras ofensivas.
     */
    protected static function filtrarOfensivas($texto)
    {
        $ofensivas = [
            'palavrÃ£o1', 'palavrÃ£o2', 'idiota', 'burro', 'estÃºpido', 'otÃ¡rio', 'merda', 'bosta', 'porra', 'caralho', 'fdp', 'foda', 'puta', 'desgraÃ§ado', 'imbecil', 'maldito', 'vagabundo', 'corno', 'cu', 'piranha', 'arrombado', 'babaca', 'cuzÃ£o', 'escroto', 'viado', 'bicha', 'boceta', 'cacete', 'pica', 'pau no cu', 'filho da puta'
        ];
        return preg_replace('/\b(' . implode('|', array_map('preg_quote', $ofensivas)) . ')\b/i', '[removido]', $texto);
    }

    public static function generate(array $data): string
    {
        $title          = trim($data['title'] ?? '');
        $businessType   = trim($data['business_type'] ?? '');
        $necessity      = trim($data['necessity'] ?? '');
        $targetAudience = trim($data['target_audience'] ?? '');
        $style          = trim($data['style'] ?? '');
        $deadline       = trim($data['deadline'] ?? '');
        $budgetRange    = trim($data['budget_range'] ?? '');

        // Clean and sanitize the core description
        $necessity = self::corrigirOrtografia($necessity);
        $necessity = self::filtrarOfensivas($necessity);
        $necessity = ucfirst(rtrim($necessity, '.') . '.');

        $partes = [];

        // Introduction line referencing service category
        if ($businessType) {
            $partes[] = "Procuro um profissional em {$businessType} para o seguinte projeto:";
        } else {
            $partes[] = "Procuro um profissional freelancer para o seguinte projeto:";
        }

        $partes[] = '';

        // Core description â€” the main text the client wrote
        $partes[] = $necessity;

        // Extra structured details
        $extras = [];
        if ($targetAudience) {
            $extras[] = "- PÃºblico-alvo: {$targetAudience}";
        }
        if ($style) {
            $extras[] = "- Estilo / ReferÃªncias: {$style}";
        }
        if ($deadline) {
            $extras[] = "- Prazo desejado: {$deadline}";
        }
        if ($budgetRange) {
            $extras[] = "- OrÃ§amento estimado: {$budgetRange}";
        }

        if (!empty($extras)) {
            $partes[] = '';
            $partes[] = "InformaÃ§Ãµes adicionais:";
            $partes   = array_merge($partes, $extras);
        }

        $partes[] = '';
        $partes[] = "Freelancers interessados, por favor enviem proposta com portfÃ³lio relevante e estimativa de prazo.";

        return trim(implode("\n", $partes));
    }
}

