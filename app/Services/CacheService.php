<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    public function cachePost($post)
    {
        $cacheKey = "post.{$post->id}";
        
        Cache::tags(['posts'])->put($cacheKey, $post, now()->addHours(24));
        
        // Cache related data
        Cache::tags(['posts'])->put(
            "post.{$post->id}.comments",
            $post->comments()->with('user')->get(),
            now()->addHours(24)
        );
    }

    public function clearCache($tags = [])
    {
        foreach ($tags as $tag) {
            Cache::tags([$tag])->flush();
        }
    }
}