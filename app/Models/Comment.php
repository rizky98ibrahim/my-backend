<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'comments';
    protected $fillable = [
        'user_id',
        'post_id',
        'comment',
        'approved',
        'is_spam',
        'is_archived',
        'ip_address',
        'user_agent',
        'referrer',
        'created_at',
        'updated_at',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
