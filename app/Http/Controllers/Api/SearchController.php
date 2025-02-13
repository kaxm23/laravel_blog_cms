<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SearchService;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;

class SearchController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|min:3',
            'category' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'date_from' => 'nullable|date',
            'sort' => 'nullable|in:created_at,updated_at,title',
            'order' => 'nullable|in:asc,desc',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:50'
        ]);

        $results = $this->searchService->search($validated);

        return response()->json([
            'hits' => $results['hits']['total']['value'],
            'posts' => PostResource::collection(collect($results['hits']['hits'])->pluck('_source'))
        ]);
    }
}