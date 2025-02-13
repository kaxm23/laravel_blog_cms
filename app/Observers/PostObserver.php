<?php

namespace App\Observers;

use App\Models\Post;
use App\Services\SearchService;

class PostObserver
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function created(Post $post)
    {
        $this->searchService->indexPost($post);
    }

    public function updated(Post $post)
    {
        $this->searchService->indexPost($post);
    }

    public function deleted(Post $post)
    {
        $this->searchService->deletePost($post->id);
    }
}