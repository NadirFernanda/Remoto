<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;
    public function candidates()
    {
        return $this->hasMany(ServiceCandidate::class);
    }
    protected $fillable = [
        'cliente_id',
        'freelancer_id',
        'titulo',
        'descricao',
        'briefing',
        'categoria',
        'prazo',
        'delivery_message',
        'service_type',
        'valor',
        'taxa',
        'valor_liquido',
        'status',
        'is_payment_released',
        'payment_released_at',
        'valor_ajuste',
        'valor_ajuste_taxa',
        'valor_ajuste_pago',
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

    public function milestones()
    {
        return $this->hasMany(Milestone::class)->orderBy('sort_order')->orderBy('id');
    }

    public function attachments()
    {
        return $this->hasMany(ServiceAttachment::class)->orderByDesc('created_at');
    }
}
