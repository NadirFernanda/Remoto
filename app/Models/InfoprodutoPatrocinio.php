<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfoprodutoPatrocinio extends Model
{
    protected $fillable = [
        'infoproduto_id',
        'user_id',
        'data_inicio',
        'data_fim',
        'dias',
        'valor_total',
        'status',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim'    => 'date',
        'valor_total' => 'float',
    ];

    public function infoproduto()
    {
        return $this->belongsTo(Infoproduto::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
