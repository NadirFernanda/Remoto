<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'saldo',
        'patrocinado',
        'patrocinio_expira_em',
        'codigo_afiliado',
        'ganhos_afiliado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
