<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_name',
        'type', // parceria, fornecedor, cliente, etc
        'status', // ativo, pendente, encerrado
        'start_date',
        'end_date',
        'document_path',
        'notes',
    ];
}
