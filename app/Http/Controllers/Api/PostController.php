<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\ApiResponseTrait;
class PostController extends Controller
{
    use AuthorizesRequests , ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::with('user')
            ->withCount([
                'reactions as likes' => function ($q) {
                    $q->where('reaction', 'like');
                },
                'reactions as dislikes' => function ($q) {
                    $q->where('reaction', 'dislike');
                },
            ]);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
               $q->where('title', 'like', '%' . $search . '%')
                   ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        if($request->has('author')){
            $query->where('user_id', $request->author);
        }

        if($request->has('sort')){
            $sortField = $request->sort;
            $sortOrder = $request->get('order' , 'desc');

            if(in_array($sortField, ['created_at', 'likes' , 'dislikes'])){
                $query->orderBy($sortField, $sortOrder);
            }
        }else{
            $query->latest();
        }

        $posts =$query->paginate($request->get('per_page', 10));


        if ($posts->isEmpty() && $search) {
            return $this->errorResponse('no posts found', [] , 404);
        }
        return $this->successResponse(new PostCollection($posts) , 'Posts received successfully.', 200);
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

        return $this->successResponse( new PostResource($post), 'Post created successfully.', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return $this->successResponse(new PostResource($post),  'Post received successfully.' , 200);
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

        return $this->successResponse(new PostResource($post->fresh()), 'Post updated successfully.', 200);

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
        return $this->successResponse(message: 'Post deleted successfully.' , status: 200);
    }
}
