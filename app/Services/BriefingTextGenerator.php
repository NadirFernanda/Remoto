<?php

namespace App\Services;

class BriefingTextGenerator
{
    /**
     * Corrige erros ortográficos simples em português (exemplo básico).
     */
    protected static function corrigirOrtografia($texto)
    {
        // Exemplo simples de correção (pode ser expandido ou integrar API externa)
        $correcoes = [
            'proficional' => 'profissional',
            'negosio' => 'negócio',
            'corre' => 'cores',
            // Adicione mais correções conforme necessário
        ];
        return str_ireplace(array_keys($correcoes), array_values($correcoes), $texto);
    }

    /**
     * Remove ou substitui palavras ofensivas.
     */
    protected static function filtrarOfensivas($texto)
    {
        $ofensivas = [
            'palavrão1', 'palavrão2', 'idiota', 'burro', 'estúpido', 'otário', 'merda', 'bosta', 'porra', 'caralho', 'fdp', 'foda', 'puta', 'desgraçado', 'imbecil', 'maldito', 'vagabundo', 'corno', 'cu', 'piranha', 'arrombado', 'babaca', 'cuzão', 'escroto', 'viado', 'bicha', 'boceta', 'cacete', 'pica', 'pau no cu', 'filho da puta'
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

        // Core description — the main text the client wrote
        $partes[] = $necessity;

        // Extra structured details
        $extras = [];
        if ($targetAudience) {
            $extras[] = "- Público-alvo: {$targetAudience}";
        }
        if ($style) {
            $extras[] = "- Estilo / Referências: {$style}";
        }
        if ($deadline) {
            $extras[] = "- Prazo desejado: {$deadline}";
        }
        if ($budgetRange) {
            $extras[] = "- Orçamento estimado: {$budgetRange}";
        }

        if (!empty($extras)) {
            $partes[] = '';
            $partes[] = "Informações adicionais:";
            $partes   = array_merge($partes, $extras);
        }

        $partes[] = '';
        $partes[] = "Freelancers interessados, por favor enviem proposta com portfólio relevante e estimativa de prazo.";

        return trim(implode("\n", $partes));
    }
}
