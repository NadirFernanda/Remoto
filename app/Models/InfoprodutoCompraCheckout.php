<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfoprodutoCompraCheckout extends Model
{
    protected $fillable = [
        'infoproduto_id',
        'comprador_id',
        'amount',
        'payment_method_used',
        'appypay_charge_id',
        'payment_reference',
        'payment_entity',
        'payment_status',
        'compra_id',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function infoproduto()
    {
        return $this->belongsTo(Infoproduto::class);
    }

    public function comprador()
    {
        return $this->belongsTo(User::class, 'comprador_id');
    }

    public function compra()
    {
        return $this->belongsTo(InfoprodutoCompra::class, 'compra_id');
    }
}
