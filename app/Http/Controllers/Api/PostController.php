<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PostResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::query()->with('user');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
               $q->where('title', 'like', '%' . $search . '%')
                   ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        $posts =$query->latest()->paginate(10);
        if ($posts->isEmpty() && $search) {
            return response()->json([
                'massage' => 'No posts found',
                'data' => []
            ] , 404);
        }
        return response()->json(['posts' => PostResource::collection($posts) , 'message' => 'Posts received successfully.'], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $path = null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
        }

        $post = Post::create([
           "user_id" => auth()->id(),
           "title" => $request["title"],
           "content" => $request["content"],
           "image" => $path,
        ]);

        return response()->json(['post' => new PostResource($post), 'message' => 'Post created successfully.'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return response()->json(['post' => new PostResource($post), 'message' => 'Post received successfully.'], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|string|min:3',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        if ($request->hasFile('image')) {
            if ($post->image){
                Storage::disk('public')->delete($post->image);
            }

            $validated['image'] = $request->file('image')->store('images', 'public');
        }

        $post->update($validated);

        return response()->json(['post' => new PostResource($post->fresh()), 'message' => 'Post updated successfully.'], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        if ($post->image){
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();
        return response()->json(['message' => 'Post deleted successfully.'], 200);
    }
}
