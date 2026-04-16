<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'service_id',
        'user_id',
        'conteudo',
        'anexo',
        'nome_original_anexo',
        'edited_at',
    ];

    protected $dates = ['edited_at', 'deleted_at'];

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
