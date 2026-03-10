<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfoprodutoCompra extends Model
{
    protected $fillable = [
        'infoproduto_id',
        'comprador_id',
        'valor_pago',
        'comissao_plataforma',
        'valor_freelancer',
    ];

    protected $casts = [
        'valor_pago'           => 'float',
        'comissao_plataforma'  => 'float',
        'valor_freelancer'     => 'float',
    ];

    public function infoproduto()
    {
        return $this->belongsTo(Infoproduto::class);
    }

    public function comprador()
    {
        return $this->belongsTo(User::class, 'comprador_id');
    }
}
