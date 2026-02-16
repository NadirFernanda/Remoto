<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletLog extends Model
{
    protected $fillable = [
        'user_id',
        'wallet_id',
        'valor',
        'tipo',
        'descricao',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
