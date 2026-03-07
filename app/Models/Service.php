<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public function candidates()
    {
        return $this->hasMany(ServiceCandidate::class);
    }
    protected $fillable = [
        'cliente_id',
        'freelancer_id',
        'titulo',
        'briefing',
        'valor',
        'taxa',
        'valor_liquido',
        'status',
        'is_payment_released',
        'payment_released_at',
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
}
