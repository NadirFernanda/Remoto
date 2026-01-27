<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Affiliate extends Model
{
    protected $fillable = [
        'user_id',
        'codigo',
        'ganhos',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
