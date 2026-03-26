<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $fillable = [
        'user_id', 'escola', 'grau', 'area_estudo',
        'ano_inicio', 'ano_fim', 'atual', 'descricao',
    ];

    protected $casts = [
        'atual' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
