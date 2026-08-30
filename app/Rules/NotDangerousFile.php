<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

/**
 * Para uploads onde o tipo de ficheiro não pode ser limitado a uma lista
 * fixa (ex: infoprodutos digitais — podem ser PDF, ZIP, vídeo, software,
 * etc.), esta regra bloqueia só os formatos genuinamente perigosos
 * (executáveis, scripts, páginas web) em vez de restringir a um formato
 * específico. A extensão é obtida a partir do conteúdo real do ficheiro
 * (via MIME detectado), não do nome enviado pelo cliente — não é possível
 * contornar renomeando "virus.exe" para "virus.pdf".
 */
class NotDangerousFile implements ValidationRule
{
    private const BLOCKED = [
        'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps', 'pht', 'phar',
        'exe', 'msi', 'bat', 'cmd', 'com', 'scr', 'dll', 'jar', 'apk',
        'sh', 'bash', 'ps1', 'vbs', 'vbe', 'wsf', 'wsh', 'hta',
        'js', 'mjs', 'jsp', 'jspx', 'asp', 'aspx', 'cgi', 'py', 'pl', 'rb',
        'html', 'htm', 'svg', 'xhtml', 'shtml',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile) {
            return;
        }

        // guessExtension() deriva do tipo MIME real (sniffed), não do nome
        // original do ficheiro — mais difícil de contornar.
        $extension = strtolower($value->guessExtension() ?: $value->getClientOriginalExtension());

        if (in_array($extension, self::BLOCKED, true)) {
            $fail('O :attribute não pode ser um ficheiro executável, script ou página web (.' . $extension . ').');
        }
    }
}
