<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletLog extends Model
{
    use SoftDeletes;
    // WalletLog é imutável: nunca fazer hard delete de registos financeiros.
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
