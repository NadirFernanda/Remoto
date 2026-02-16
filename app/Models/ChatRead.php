<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Message;

class ChatRead extends Model
{
    protected $fillable = ['service_id', 'user_id', 'last_read_at'];

    public static function markRead(int $serviceId, int $userId)
    {
        return self::updateOrCreate(
            ['service_id' => $serviceId, 'user_id' => $userId],
            ['last_read_at' => now()]
        );
    }

    public static function unreadCount(int $serviceId, int $userId): int
    {
        $last = self::where('service_id', $serviceId)->where('user_id', $userId)->value('last_read_at');
        $q = Message::where('service_id', $serviceId)->where('user_id', '<>', $userId);
        if ($last) {
            $q->where('created_at', '>', $last);
        }
        return $q->count();
    }
}
