<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'service_id',
        'user_id',
        'conteudo',
        'anexo',
        'nome_original_anexo',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAnexoUrlAttribute()
    {
        return $this->anexo ? asset('storage/anexos/' . $this->anexo) : null;
    }
}
