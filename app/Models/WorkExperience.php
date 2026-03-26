<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkExperience extends Model
{
    protected $fillable = [
        'user_id', 'titulo', 'empresa', 'cidade', 'pais',
        'mes_inicio', 'ano_inicio', 'mes_fim', 'ano_fim', 'atual', 'descricao',
    ];

    protected $casts = [
        'atual' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
