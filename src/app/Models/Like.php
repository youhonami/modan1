<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tweet_id',
    ];

    /**
     * いいねしたユーザー
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * いいね対象のツイート
     */
    public function tweet()
    {
        return $this->belongsTo(Tweet::class);
    }
}
