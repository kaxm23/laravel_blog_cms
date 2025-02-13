<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_post()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/posts', [
            'title' => 'Test Post',
            'content' => 'Test content',
            'status' => 'published',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', ['title' => 'Test Post']);
    }

    public function test_post_requires_title_and_content()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/posts', []);

        $response->assertSessionHasErrors(['title', 'content']);
    }
}