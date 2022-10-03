<?php

namespace Database\Factories;

use App\Models\PostStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Post default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->name(),
            'slug' => fake()->slug(),
            'content' => fake()->text(10000),
            'author' => fake()->firstName() . ' ' . fake()->lastName(),
            'status' => Arr::random(PostStatus::all())
        ];
    }

    /**
     * Sets the Post to draft status
     *
     * @return PostFactory
     */
    public function draft()
    {

        return $this->state(function (array $attributes) {

            return [
                'status' => PostStatus::DRAFT
            ];
        });
    }
}
