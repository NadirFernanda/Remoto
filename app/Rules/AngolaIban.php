<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * IBAN angolano: "AO" + 2 dígitos de controlo + 21 dígitos da conta (BBAN),
 * 25 caracteres no total. Sem esta validação, números claramente incompletos
 * (ex.: "AO06 0006") passavam sem qualquer aviso.
 */
class AngolaIban implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $normalized = strtoupper(preg_replace('/[\s-]+/', '', (string) $value));

        if (! preg_match('/^AO\d{23}$/', $normalized)) {
            $fail('O :attribute deve ser um IBAN angolano válido: "AO" seguido de 23 dígitos (25 caracteres no total, ex.: AO06 0006 0000 0000 0000 0000 0).');
        }
    }
}
