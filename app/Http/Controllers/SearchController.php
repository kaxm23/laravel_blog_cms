<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Elasticsearch\Client;

class SearchController extends Controller
{
    protected $elasticsearch;

    public function __construct(Client $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        $filters = $request->input('filters', []);

        $results = $this->elasticsearch->search([
            'index' => 'posts',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            'multi_match' => [
                                'query' => $query,
                                'fields' => ['title^3', 'content', 'tags'],
                            ],
                        ],
                        'filter' => $this->buildFilters($filters),
                    ],
                ],
                'highlight' => [
                    'fields' => [
                        'title' => new \stdClass(),
                        'content' => new \stdClass(),
                    ],
                ],
            ],
        ]);

        return view('search.results', compact('results'));
    }
}