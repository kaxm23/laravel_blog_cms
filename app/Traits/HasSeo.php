<?php

namespace App\Traits;

trait HasSeo
{
    public function getSeoTitle()
    {
        return $this->meta_data['seo_title'] ?? $this->title;
    }

    public function getSeoDescription()
    {
        return $this->meta_data['seo_description'] ?? $this->excerpt;
    }

    public function getSeoKeywords()
    {
        return $this->meta_data['seo_keywords'] ?? '';
    }

    public function getStructuredData()
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $this->getSeoTitle(),
            'description' => $this->getSeoDescription(),
            'author' => [
                '@type' => 'Person',
                'name' => $this->user->name,
            ],
            'datePublished' => $this->published_at->toIso8601String(),
            'dateModified' => $this->updated_at->toIso8601String(),
            'image' => $this->featured_image,
        ];
    }
}