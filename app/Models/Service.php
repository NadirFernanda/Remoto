<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;
    public function candidates()
    {
        return $this->hasMany(ServiceCandidate::class);
    }
    /**
     * Detects and fixes double-encoded UTF-8 strings.
     *
     * Symptom: accented characters display as two garbled characters,
     * e.g. "ó" stored correctly as UTF-8 bytes C3 B3 was re-encoded as
     * if they were Latin-1, producing "Ã³" (bytes C3 83 C2 B3).
     *
     * Safe: pure ASCII strings and correctly-encoded strings are returned
     * unchanged. Only applies the fix when the ISO-8859-1 reinterpretation
     * of the string is itself valid UTF-8 (the classic double-encoding pattern).
     */
    public static function fixDoubleEncodedUtf8(string $str): string
    {
        // No non-ASCII chars → nothing to fix
        if (!preg_match('/[\x{00C0}-\x{00FF}]/u', $str)) {
            return $str;
        }
        // Reinterpret each multi-byte code-unit as a Latin-1 byte
        $decoded = mb_convert_encoding($str, 'ISO-8859-1', 'UTF-8');
        // If the result is still valid UTF-8, it was double-encoded
        if (mb_check_encoding($decoded, 'UTF-8')) {
            return $decoded;
        }
        return $str;
    }

    /**
     * Accessor: strip JSON-wrapping quotes from legacy briefing values.
     * Old rows stored as JSON string (e.g. '"My text"') render correctly as plain text.
     */
    public function getBriefingAttribute(mixed $value): ?string
    {
        if ($value === null) return null;
        $str = self::fixDoubleEncodedUtf8((string) $value);
        $decoded = json_decode($str, true);
        return (json_last_error() === JSON_ERROR_NONE && is_string($decoded)) ? $decoded : $str;
    }

    protected $fillable = [
        'cliente_id',
        'freelancer_id',
        'titulo',
        'descricao',
        'briefing',
        'categoria',
        'prazo',
        'delivery_message',
        'service_type',
        'valor',
        'taxa',
        'valor_liquido',
        'status',
        'is_payment_released',
        'payment_released_at',
        'valor_ajuste',
        'valor_ajuste_taxa',
        'valor_ajuste_pago',
    ];

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class)->orderBy('sort_order')->orderBy('id');
    }

    public function attachments()
    {
        return $this->hasMany(ServiceAttachment::class)->orderByDesc('created_at');
    }
}
