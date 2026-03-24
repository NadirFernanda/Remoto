<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dispute extends Model
{
    use SoftDeletes;
    protected $fillable = ['service_id', 'opened_by', 'reason', 'description', 'status', 'admin_note'];

    public static array $reasons = [
        'atraso'       => 'Atraso na entrega',
        'qualidade'    => 'Qualidade insuficiente',
        'nao_pagamento'=> 'Não pagamento',
        'outro'        => 'Outro motivo',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function opener()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function messages()
    {
        return $this->hasMany(DisputeMessage::class);
    }
}
