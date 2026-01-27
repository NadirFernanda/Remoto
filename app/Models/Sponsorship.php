<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sponsorship extends Model
{
    protected $fillable = [
        'user_id',
        'plano',
        'status',
        'data_inicio',
        'data_fim',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
