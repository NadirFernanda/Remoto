<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    protected $fillable = [
        'service_id', 'title', 'description',
        'due_date', 'completed', 'completed_at', 'sort_order',
    ];

    protected $casts = [
        'completed'    => 'boolean',
        'due_date'     => 'date',
        'completed_at' => 'datetime',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function markComplete(): void
    {
        $this->update(['completed' => true, 'completed_at' => now()]);
    }

    public function markIncomplete(): void
    {
        $this->update(['completed' => false, 'completed_at' => null]);
    }
}
