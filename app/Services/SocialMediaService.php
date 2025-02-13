<?php

namespace App\Services;

use Abraham\TwitterOAuth\TwitterOAuth;
use Facebook\Facebook;
use LinkedIn\Client;

class SocialMediaService
{
    protected $twitter;
    protected $facebook;
    protected $linkedin;

    public function __construct()
    {
        $this->twitter = new TwitterOAuth(
            config('services.twitter.key'),
            config('services.twitter.secret')
        );

        $this->facebook = new Facebook([
            'app_id' => config('services.facebook.client_id'),
            'app_secret' => config('services.facebook.client_secret'),
        ]);
    }

    public function sharePost($post)
    {
        // Share to Twitter
        $this->twitter->post('statuses/update', [
            'status' => $post->title . ' ' . route('posts.show', $post),
        ]);

        // Share to Facebook
        $this->facebook->post('/me/feed', [
            'message' => $post->title,
            'link' => route('posts.show', $post),
        ]);
    }
}