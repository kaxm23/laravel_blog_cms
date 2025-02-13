<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->authorizeResource(Post::class, 'post');
    }

    public function index()
    {
        $posts = Cache::remember('posts.page.' . request('page', 1), 3600, function () {
            return Post::with(['user', 'categories'])
                ->published()
                ->latest('published_at')
                ->paginate(12);
        });

        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(StorePostRequest $request)
    {
        $post = Post::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'status' => $request->status,
            'published_at' => $request->status === 'published' ? now() : null,
            'meta_data' => [
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
            ],
        ]);

        if ($request->hasFile('featured_image')) {
            $post->addMediaFromRequest('featured_image')
                ->toMediaCollection('featured_images');
        }

        $post->categories()->sync($request->categories);
        $post->tags()->sync($request->tags);

        Cache::tags(['posts'])->flush();

        return redirect()->route('posts.edit', $post)
            ->with('success', 'Post created successfully.');
    }

    public function show(Post $post)
    {
        $post->load(['user', 'categories', 'tags', 'comments.user']);
        
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    public function update(StorePostRequest $request, Post $post)
    {
        $post->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'status' => $request->status,
            'published_at' => $request->status === 'published' ? now() : null,
            'meta_data' => [
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
            ],
        ]);

        if ($request->hasFile('featured_image')) {
            $post->clearMediaCollection('featured_images');
            $post->addMediaFromRequest('featured_image')
                ->toMediaCollection('featured_images');
        }

        $post->categories()->sync($request->categories);
        $post->tags()->sync($request->tags);

        Cache::tags(['posts'])->flush();

        return redirect()->route('posts.edit', $post)
            ->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        Cache::tags(['posts'])->flush();

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully.');
    }
}