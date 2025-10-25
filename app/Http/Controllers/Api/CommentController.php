<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class CommentController extends Controller
{
    use AuthorizesRequests , ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Post $post)
    {
        return $this->successResponse(
            new CommentCollection($post->comments()->with('user')->paginate(10)),
            'Comments retrieved successfully',
          200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request , Post $post)
    {
        $validated = $request->validate(['body' => 'required|string|max:255']);

        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'body' => $validated['body']
        ]);


        return $this->successResponse( new CommentResource($comment->load('user')) ,  'Comment successfully posted.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        return $this->successResponse(new CommentResource($comment->load('user')), 'Comment retrieved successfully', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $validated = $request->validate([
            'body' => 'required|string|max:255'
        ]);

        $UpdatedComment = $comment->update($validated);

        return $this->successResponse( CommentResource($UpdatedComment->load('user')) , 'Comment updated successfully.' , 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();
        return $this->successResponse(massage: 'Comment deleted successfully.', status:  200);
    }
}
