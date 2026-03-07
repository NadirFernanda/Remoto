<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'media_path', 'media_type',
        'external_url', 'is_public', 'category', 'issuer', 'issued_year',
        'sort_order', 'featured',
    ];

    protected $casts = [
        'featured'  => 'boolean',
        'is_public' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
