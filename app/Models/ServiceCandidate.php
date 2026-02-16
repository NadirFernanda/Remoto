<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCandidate extends Model
{
    protected $fillable = [
        'service_id',
        'freelancer_id',
        'status', // pending, chosen, rejected
        'proposal_message',
        'proposal_value',
        'proposal_fee',
        'proposal_net',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }
}
