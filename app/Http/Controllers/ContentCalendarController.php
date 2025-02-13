<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class ContentCalendarController extends Controller
{
    public function index()
    {
        $scheduledPosts = Post::where('status', 'scheduled')
            ->orderBy('published_at')
            ->get();

        return view('content-calendar.index', [
            'posts' => $scheduledPosts,
        ]);
    }

    public function schedule(Request $request, Post $post)
    {
        $post->update([
            'status' => 'scheduled',
            'published_at' => $request->published_at,
        ]);

        return response()->json([
            'message' => 'Post scheduled successfully',
        ]);
    }
}