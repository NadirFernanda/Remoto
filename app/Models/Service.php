<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'cliente_id',
        'freelancer_id',
        'titulo',
        'briefing',
        'valor',
        'taxa',
        'valor_liquido',
        'status',
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
