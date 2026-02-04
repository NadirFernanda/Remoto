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
        $partes = [];
        if (!empty($data['business_type'])) {
            $partes[] = "Meu negócio é do tipo: {$data['business_type']}.";
        }
        if (!empty($data['target_audience'])) {
            $partes[] = "O público-alvo que desejo atingir é: {$data['target_audience']}.";
        }
        if (!empty($data['style'])) {
            $partes[] = "O estilo desejado para o projeto é: {$data['style']}.";
        }
        if (!empty($data['colors'])) {
            $partes[] = "Tenho preferência pelas cores: {$data['colors']}.";
        }
        if (!empty($data['usage'])) {
            $partes[] = "O material/serviço será utilizado em: {$data['usage']}.";
        }
        $texto = "Desejo um serviço/projeto profissional. ";
        $texto .= implode(' ', $partes);
        // Corrigir ortografia e filtrar palavras ofensivas
        $texto = self::corrigirOrtografia($texto);
        $texto = self::filtrarOfensivas($texto);
        return trim($texto);
    }
}
