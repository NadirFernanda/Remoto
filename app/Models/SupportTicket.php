<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = [
        'user_id', 'category', 'subject', 'message', 'status', 'priority',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(SupportTicketReply::class, 'ticket_id')->orderBy('created_at');
    }

    public function latestReply()
    {
        return $this->hasOne(SupportTicketReply::class, 'ticket_id')->latestOfMany();
    }

    public static function categoryLabel(string $cat): string
    {
        return match($cat) {
            'pagamento'  => 'Pagamento',
            'projecto'   => 'Projecto',
            'conta'      => 'Conta',
            'tecnico'    => 'Técnico',
            default      => 'Outro',
        };
    }

    public static function priorityLabel(string $p): string
    {
        return match($p) {
            'alta'    => 'Alta',
            'urgente' => 'Urgente',
            default   => 'Normal',
        };
    }

    public static function statusLabel(string $s): string
    {
        return match($s) {
            'em_andamento' => 'Em Andamento',
            'fechado'      => 'Fechado',
            default        => 'Aberto',
        };
    }
}
