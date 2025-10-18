<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Post $post)
    {
        return response()->json([
            'comments' => $post->comments()->with('user:id,name')->latest()->get(),
            'message' => 'Comments retrieved successfully'
        ] , 200);
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


        return response()->json(['comment' => $comment , 'message' => 'Comment successfully posted.'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        return response()->json([
            'comments' =>$comment->with('user:id,name')->latest()->get(),
            'message' => 'Comment retrieved successfully'
        ] , 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        if (auth()->id() !== $comment->user_id){
            return response()->json(['message' => 'Access denied'], 403);
        }

        $validated = $request->validate([
            'body' => 'required|string|max:255'
        ]);

        $UpdatedComment = $comment->update($validated);

        return response()->json(['comment' => $UpdatedComment , 'message' => 'Comment updated successfully.'], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        if (auth()->id() !== $comment->user_id){
            return response()->json(['message' => 'Access denied'], 403);
        }

        $comment->delete();
        return response()->json(['message' => 'Comment deleted successfully.'], 200);
    }
}
