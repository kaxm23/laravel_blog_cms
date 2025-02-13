<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostVersion extends Model
{
    protected $fillable = [
        'post_id',
        'user_id',
        'title',
        'content',
        'version_number',
        'changes',
    ];

    protected $casts = [
        'changes' => 'array',
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