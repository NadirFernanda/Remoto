<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CreatorProfile extends Model
{
    // Platform-wide fixed subscription price (non-negotiable)
    const SUBSCRIPTION_PRICE = 3000.00;

    protected $fillable = [
        'user_id',
        'category',
        'bio',
        'cover_photo',
        'is_public',
        'total_subscribers',
        'total_earnings',
    ];

    protected $casts = [
        'total_earnings' => 'float',
        'is_public'      => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coverPhotoUrl(): string
    {
        if ($this->cover_photo) {
            return Storage::url($this->cover_photo);
        }
        return asset('img/default-cover.jpg');
    }

    public static function categories(): array
    {
        return [
            'entretenimento' => 'Entretenimento',
            'educacao'       => 'Educação',
            'tecnologia'     => 'Tecnologia',
            'negocios'       => 'Negócios',
            'saude'          => 'Saúde & Bem-estar',
            'moda'           => 'Moda & Beleza',
            'gastronomia'    => 'Gastronomia',
            'viagens'        => 'Viagens',
            'desporto'       => 'Desporto',
            'arte'           => 'Arte & Design',
            'musica'         => 'Música',
            'geral'          => 'Geral',
        ];
    }
}
