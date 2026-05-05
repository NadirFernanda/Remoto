<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'user_id',
        'reason',
        'details',
        'status',
        'evidence_paths',
        'valor_reembolso',
        'proposta_cliente',
        'proposta_freelancer',
        'resposta_freelancer',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
