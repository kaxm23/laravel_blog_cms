<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\SearchService;
use Illuminate\Console\Command;

class SetupElasticsearch extends Command
{
    protected $signature = 'elasticsearch:setup';
    protected $description = 'Set up Elasticsearch indices and import existing data';

    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        parent::__construct();
        $this->searchService = $searchService;
    }

    public function handle()
    {
        $this->info('Creating Elasticsearch index...');
        $this->searchService->createIndex();

        $this->info('Indexing existing posts...');
        Post::chunk(100, function ($posts) {
            foreach ($posts as $post) {
                $this->searchService->indexPost($post);
            }
        });

        $this->info('Setup completed successfully!');
    }
}