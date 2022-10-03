<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PostStatus;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PostTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test that GET /post/{id} returns an individual post
     *
     * @return void
     */
    public function test_get_a_post()
    {

        $post = Post::factory()->create();

        $response = $this->get("/api/posts/{$post->id}");

        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) => $json->hasAll(['id', 'title', 'status', 'slug', 'content', 'author', 'published_at', 'created_at', 'updated_at'])
            ->where('id', $post->id)
            ->where('title', $post->title)
            ->where('status', $post->status)
            ->where('content', $post->content)
        );
    }

    /**
     * Test that GET /posts returns a list of posts without drafts
     *
     * @return void
     */
    public function test_list_posts()
    {

        $posts = Post::factory()->count(100)->create();

        $response = $this->get("/api/posts");

        $published = $posts->filter(function ($post) {

            return $post->status === 'published';
        });

        $response->assertStatus(200);
        $response->assertJsonPath('total', $published->count());
    }

    /**
     * Test that GET /posts?drafts=true includes all posts even drafts
     *
     * @return void
     */
    public function test_list_with_drafts()
    {

        $amount = 100;
        Post::factory()->count($amount)->create();

        $response = $this->get("/api/posts?drafts=true");

        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonPath('total', 100);

        $page2 = $this->get("/api/posts?drafts=true&page=2");

        $page2->assertJsonPath('current_page', 2);
    }


    /**
     * Test that GET /posts can be paginated
     *
     * @return void
     */
    public function test_paginate_list_results()
    {

        $amount = 100;
        Post::factory()->count($amount)->create();

        $response = $this->get("/api/posts");

        $response->assertStatus(200);
    }

    /**
     * Test that POST /post creates a new post
     *
     * @return void
     */
    public function test_create_post()
    {
        $post = [
            'title' => 'First blog post',
            'slug' => 'first-blog-post',
            'content' => 'Some random content for my first test',
            'author' => 'John Doe'
        ];

        $response = $this->post("/api/posts", $post);

        $response->assertStatus(201);
        $response->assertJson(fn(AssertableJson $json) => $json
            ->hasAll(['id', 'title', 'slug', 'status', 'content', 'author', 'published_at', 'created_at', 'updated_at'])
            ->where('title', $post['title'])
            ->where('slug', $post['slug'])
            ->where('status', PostStatus::DRAFT)
            ->where('content', $post['content'])
            ->where('published_at', null)
        );
    }

    /**
     * Test that PATCH /post updates an existing post
     *
     * @return void
     */
    public function test_update_post()
    {

        $post = Post::factory()->create();
        $update = array(
            'title' => 'new title',
            'content' => 'new content'
        );

        $response = $this->patch("/api/posts/{$post->id}", $update);

        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) => $json->hasAll(['id', 'title', 'slug', 'status', 'content', 'author', 'published_at', 'created_at', 'updated_at'])
            ->where('title', $update['title'])
            ->where('content', $update['content'])
            ->where('slug', $post->slug)
        );
    }

    /**
     * Test that the published date is set to now when the status of the post is changed to published
     *
     * @return void
     */
    public function test_update_set_published_when_status_changed()
    {

        $post = Post::factory()->draft()->create();
        $update = array(
            'status' => PostStatus::PUBLISHED
        );

        $response = $this->patch("/api/posts/{$post->id}", $update);

        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) => $json->hasAll(['id', 'title', 'slug', 'status', 'content', 'author', 'published_at', 'created_at', 'updated_at'])
            ->where('status', PostStatus::PUBLISHED)
            ->whereNot('published_at', null)
        );
    }

    /**
     * Test that DELETE /post/{id} deletes an existing post
     *
     * @return void
     */
    public function test_delete_post()
    {

        $post = Post::factory()->create();

        $response = $this->delete("/api/posts/{$post->id}");

        $response->assertStatus(204);
    }

    /**
     * Test can not delete a non existant post
     * @return void
     */
    public function test_not_delete_invalid_post()
    {

        $response = $this->delete("/api/posts/1");

        $response->assertStatus(404);
    }

}
