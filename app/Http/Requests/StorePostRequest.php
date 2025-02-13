<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => ['required', 'string', 'max:255', 'unique:posts,title,' . $this->post?->id],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:draft,pending,published,scheduled'],
            'published_at' => ['nullable', 'date'],
            'categories' => ['required', 'array'],
            'categories.*' => ['exists:categories,id'],
            'featured_image' => ['nullable', 'image', 'max:5120'], // 5MB max
            'meta_title' => ['nullable', 'string', 'max:60'],
            'meta_description' => ['nullable', 'string', 'max:160'],
        ];
    }
}