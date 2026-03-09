<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    protected $fillable = [
        'sender_id', 'recipient_id', 'title', 'message', 'value', 'fee', 'net', 'type', 'status', 'service_id'
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
