<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostStoreRequest;
use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of posts. The "draft" query parameter
     * can be used to show or hide "draft" posts, by default
     * they are hidden Pass ?draft=true to show them as well
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $posts = Post::CreatedByAsc();

        if (!$request->query('drafts') || $request->query('drafts') !== 'true') {

            $posts->HideDrafts();
        }

        return response()->json($posts->paginate(10));
    }

    /**
     * Store a newly created post in storage.
     *
     * @param \App\Http\Requests\PostStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PostStoreRequest $request)
    {

        $post = Post::create([
            'title' => $request->title,
            'slug' => $request->slug,
            'author' => $request->author,
            'content' => $request->content
        ]);

        return response()->json($post, 201);
    }

    /**
     * Display the post.
     *
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Post $post)
    {

        return response()->json($post);
    }

    /**
     * Update the post in storage.
     *
     * @param \App\Http\Requests\PostStoreRequest $request
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PostStoreRequest $request, Post $post)
    {

        $post->update($request->safe()->toArray());

        return response()->json(Post::find($post->id));
    }

    /**
     * Remove the post from storage.
     *
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json(null, 204);
    }
}
