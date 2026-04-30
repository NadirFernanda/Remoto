<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminReceipt extends Model
{
    protected $table = 'admin_receipts';

    protected $fillable = [
        'receipt_number',
        'nome',
        'nif',
        'telefone',
        'endereco',
        'start_date',
        'end_date',
        'notes',
        'document_path',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Gera o próximo número de recibo sequencial: REC-AAAA-NNNN
     */
    public static function generateNumber(): string
    {
        $year = now()->year;
        $last = static::whereYear('created_at', $year)->max('id') ?? 0;
        $seq  = str_pad($last + 1, 4, '0', STR_PAD_LEFT);
        return "REC-{$year}-{$seq}";
    }
}
