<?php

namespace App\Services;

class BriefingTextGenerator
{
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
        return trim($texto);
    }
}
