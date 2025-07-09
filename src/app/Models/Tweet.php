<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
    ];

    /**
     * 投稿したユーザー
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * コメント一覧
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * いいね一覧
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
}
