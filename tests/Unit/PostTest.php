<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PostTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test slug does not contain any invalid characters
     *
     * @return void
     */
    function test_slug_can_not_contain_special_chars()
    {

        $invalidSlugs = [
            'with space',
            'with%char',
            'withaccént',
            'withexclamation!',
            'with--double-dash',
            'endingwithdash-',
            '-'
        ];

        $post = [
            'title' => 'First blog post',
            'content' => 'Some random content for my first test',
            'author' => 'John Doe'
        ];

        foreach ($invalidSlugs as $slug) {

            $this->json('POST', "/api/posts", [...$post, 'slug' => $slug])->assertStatus(422);
        }
    }

    /**
     * Test that the slug should be unique
     *
     * @return void
     */
    function test_create_slug_should_be_unique()
    {

        $post = [
            'title' => 'First blog post',
            'slug' => 'first-blog-post',
            'content' => 'Some random content for my first test',
            'author' => 'John Doe'
        ];

        $response = $this->post("/api/posts", $post);
        $response->assertStatus(201);

        $responseFail = $this->json('POST', "/api/posts", $post);
        $responseFail->assertStatus(422);
    }

    /**
     * Test that the status should only be one of "published" or "draft"
     *
     * @return void
     */
    function test_status_in_list()
    {

        $post = [
            'title' => 'First blog post',
            'slug' => 'first-blog-post',
            'content' => 'Some random content for my first test',
            'author' => 'John Doe',
            'status' => 'invalid'
        ];

        $responseFail = $this->json('POST', "/api/posts", $post);
        $responseFail->assertStatus(422);
    }

    /**
     * Test that the content must be defined
     *
     * @return void
     */
    function test_content_not_empty()
    {

        $post = [
            'title' => 'First blog post',
            'slug' => 'first-blog-post',
            'content' => '',
            'author' => 'John Doe'
        ];

        $responseFail = $this->json('POST', "/api/posts", $post);
        $responseFail->assertStatus(422);
    }

    /**
     * Test content should be at least 10 characters long
     *
     * @return void
     */
    function test_content_min_10_chars()
    {

        $post = [
            'title' => 'First blog post',
            'slug' => 'first-blog-post',
            'content' => 'aaaaaaaaa',
            'author' => 'John Doe'
        ];

        $responseFail = $this->json('POST', "/api/posts", $post);
        $responseFail->assertStatus(422);
    }

    /**
     * Test that the title can not be empty
     *
     * @return void
     */
    function test_title_not_empty()
    {

        $post = [
            'title' => '',
            'slug' => 'first-blog-post',
            'content' => 'a correct bit of content',
            'author' => 'John Doe'
        ];

        $responseFail = $this->json('POST', "/api/posts", $post);
        $responseFail->assertStatus(422);
    }

    /**
     * Test title should have at least 3 chars
     *
     * @return void
     */
    function test_title_min_3_chars()
    {

        $post = [
            'title' => 'za',
            'slug' => 'first-blog-post',
            'content' => 'a correct bit of content',
            'author' => 'John Doe'
        ];

        $responseFail = $this->json('POST', "/api/posts", $post);
        $responseFail->assertStatus(422);
    }

    /**
     * Test title should have maximum 255 characters
     *
     * @return void
     */
    function test_title_max_255_chars()
    {

        $post = [
            'title' => 'this is 255 characters long at least it should be by now as this is very long. this is 255 characters long at least it should be by now as this is very longthis is 255 characters long at least it should be by now as this is very long. this is 255 characters long at least it should be by now as this is very long',
            'slug' => 'first-blog-post',
            'content' => 'a correct bit of content',
            'author' => 'John Doe'
        ];

        $responseFail = $this->json('POST', "/api/posts", $post);
        $responseFail->assertStatus(422);
    }

    /**
     * Test author has minimum of 3 characters
     *
     * @return void
     */
    function test_author_min_3_chars()
    {

        $post = [
            'title' => 'this is title',
            'slug' => 'first-blog-post',
            'content' => 'a correct bit of content',
            'author' => 'Jo'
        ];

        $responseFail = $this->json('POST', "/api/posts", $post);
        $responseFail->assertStatus(422);
    }

    /**
     * Test author has maximum of 100 characters
     *
     * @return void
     */
    function test_author_max_100_chars()
    {

        $post = [
            'title' => 'this is a title',
            'slug' => 'first-blog-post',
            'content' => 'a correct bit of content',
            'author' => 'John Doewithanextremlylongsurnamesothatitgoesover100charsnoteventhereyetsoneedtokeepgoingabitmoreeeee'
        ];

        $responseFail = $this->json('POST', "/api/posts", $post);
        $responseFail->assertStatus(422);
    }
}
