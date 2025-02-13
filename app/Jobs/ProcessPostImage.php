<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\Facades\Image;
use App\Models\Post;

class ProcessPostImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected Post $post,
        protected string $imagePath
    ) {}

    public function handle()
    {
        // Process featured image
        $image = Image::make(storage_path('app/public/' . $this->imagePath));
        
        // Create thumbnails
        $image->fit(300, 300)->save(storage_path('app/public/thumbnails/' . basename($this->imagePath)));
        
        // Optimize original
        $image->save(storage_path('app/public/' . $this->imagePath), 80);
    }
}