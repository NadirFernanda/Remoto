<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;
    protected $fillable = ['author_id', 'target_id', 'service_id', 'rating', 'comment'];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function target()
    {
        return $this->belongsTo(User::class, 'target_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
