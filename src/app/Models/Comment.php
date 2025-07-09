<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tweet_id',
        'body',
    ];

    /**
     * コメントを投稿したユーザー
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * コメントされたツイート
     */
    public function tweet()
    {
        return $this->belongsTo(Tweet::class);
    }
}
