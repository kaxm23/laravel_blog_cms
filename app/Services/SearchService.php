<?php

namespace App\Services;

use Elasticsearch\Client;
use App\Models\Post;
use Exception;

class SearchService
{
    protected $elasticsearch;
    protected $index = 'posts';

    public function __construct(Client $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function createIndex()
    {
        $params = [
            'index' => $this->index,
            'body' => [
                'settings' => config('elasticsearch.indices.settings'),
                'mappings' => [
                    'properties' => [
                        'id' => ['type' => 'integer'],
                        'title' => [
                            'type' => 'text',
                            'analyzer' => 'custom_analyzer',
                            'fields' => [
                                'keyword' => ['type' => 'keyword']
                            ]
                        ],
                        'content' => [
                            'type' => 'text',
                            'analyzer' => 'custom_analyzer'
                        ],
                        'tags' => ['type' => 'keyword'],
                        'category' => ['type' => 'keyword'],
                        'author_id' => ['type' => 'integer'],
                        'created_at' => ['type' => 'date'],
                        'updated_at' => ['type' => 'date']
                    ]
                ]
            ]
        ];

        return $this->elasticsearch->indices()->create($params);
    }

    public function indexPost(Post $post)
    {
        $params = [
            'index' => $this->index,
            'id' => $post->id,
            'body' => [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'tags' => $post->tags->pluck('name')->toArray(),
                'category' => $post->category->name,
                'author_id' => $post->author_id,
                'created_at' => $post->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $post->updated_at->format('Y-m-d H:i:s')
            ]
        ];

        return $this->elasticsearch->index($params);
    }

    public function search(array $query)
    {
        $params = [
            'index' => $this->index,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [],
                        'filter' => []
                    ]
                ],
                'sort' => [],
                'from' => ($query['page'] ?? 1) - 1,
                'size' => $query['per_page'] ?? 15
            ]
        ];

        // Full-text search
        if (!empty($query['search'])) {
            $params['body']['query']['bool']['must'][] = [
                'multi_match' => [
                    'query' => $query['search'],
                    'fields' => ['title^3', 'content', 'tags']
                ]
            ];
        }

        // Filters
        if (!empty($query['category'])) {
            $params['body']['query']['bool']['filter'][] = [
                'term' => ['category' => $query['category']]
            ];
        }

        if (!empty($query['tags'])) {
            $params['body']['query']['bool']['filter'][] = [
                'terms' => ['tags' => $query['tags']]
            ];
        }

        if (!empty($query['date_from'])) {
            $params['body']['query']['bool']['filter'][] = [
                'range' => [
                    'created_at' => ['gte' => $query['date_from']]
                ]
            ];
        }

        // Sorting
        if (!empty($query['sort'])) {
            $params['body']['sort'][] = [$query['sort'] => ['order' => $query['order'] ?? 'desc']];
        }

        return $this->elasticsearch->search($params);
    }

    public function deletePost($postId)
    {
        $params = [
            'index' => $this->index,
            'id' => $postId
        ];

        return $this->elasticsearch->delete($params);
    }
}