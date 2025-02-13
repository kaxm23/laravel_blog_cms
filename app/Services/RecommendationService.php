<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Phpml\Classification\SVC;
use Phpml\FeatureExtraction\TfIdfTransformer;

class RecommendationService
{
    public function getRecommendedPosts(User $user)
    {
        // Get user's reading history
        $userHistory = $user->readPosts()->pluck('posts.id')->toArray();
        
        // Get post features
        $posts = Post::published()->get();
        $features = $this->extractFeatures($posts);
        
        // Train model
        $labels = $this->generateLabels($posts, $userHistory);
        $classifier = new SVC();
        $classifier->train($features, $labels);
        
        // Get recommendations
        return $posts->filter(function ($post) use ($classifier, $features) {
            $index = $post->id - 1;
            return $classifier->predict([$features[$index]]) === 1;
        });
    }

    private function extractFeatures($posts)
    {
        $transformer = new TfIdfTransformer();
        return $transformer->transform(
            $posts->map(function ($post) {
                return $post->content . ' ' . $post->title;
            })->toArray()
        );
    }

    private function generateLabels($posts, $userHistory)
    {
        return $posts->map(function ($post) use ($userHistory) {
            return in_array($post->id, $userHistory) ? 1 : 0;
        })->toArray();
    }
}