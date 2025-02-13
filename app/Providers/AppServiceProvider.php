<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Implement eager loading optimization
        Model::preventLazyLoading(!app()->isProduction());
        
        // Add global caching for frequently accessed data
        Cache::remember('global_categories', 3600, function () {
            return Category::withCount('posts')->get();
        });
    }
}