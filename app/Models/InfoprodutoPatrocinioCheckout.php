<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfoprodutoPatrocinioCheckout extends Model
{
    protected $fillable = [
        'infoproduto_id',
        'user_id',
        'dias',
        'amount',
        'payment_method_used',
        'appypay_charge_id',
        'payment_status',
        'patrocinio_id',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function infoproduto()
    {
        return $this->belongsTo(Infoproduto::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function patrocinio()
    {
        return $this->belongsTo(InfoprodutoPatrocinio::class, 'patrocinio_id');
    }
}
