<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashFlowClosing extends Model
{
    protected $fillable = [
        'data',
        'grupos',
        'total_entradas',
        'total_saidas',
        'total_comissao',
        'saldo_liquido',
        'saldo_acumulado',
        'fechado_por',
    ];

    protected $casts = [
        'data'   => 'date',
        'grupos' => 'array',
    ];
}
