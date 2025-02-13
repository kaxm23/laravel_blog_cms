<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;

/**
 * @OA\Info(
 *     title="Blog CMS API",
 *     version="1.0.0"
 * )
 */
class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/posts",
     *     summary="Get list of posts",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function index()
    {
        return PostResource::collection(
            Post::published()->paginate(15)
        );
    }
}