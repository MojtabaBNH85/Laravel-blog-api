<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with("user:id,name")->latest()->get();
        return response()->json(['posts' => $posts , 'message' => 'Posts received successfully.'], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required'
        ]);
        $post = Post::create([
           "user_id" => auth()->id(),
           "title" => $request["title"],
           "content" => $request["content"]
        ]);

        return response()->json(['post' => $post, 'message' => 'Post created successfully.'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return response()->json(['post' => $post, 'message' => 'Post received successfully.'], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $request->validate([
            'title' => 'required|string|min:3',
            'content' => 'required|string'
        ]);

        $UpdatedPost = $post->update([
           'title' => $request["title"],
           'content' => $request["content"]
        ]);

        return response()->json(['post' => $UpdatedPost, 'message' => 'Post updated successfully.'], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();
        return response()->json(['message' => 'Post deleted successfully.'], 200);
    }
}
