<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    public function react(Request $request , Post $post)
    {
        $validated = $request->validate([
            'reaction' => 'required|in:like,dislike'
        ]);

        $user = $request->user();

        $existing = $user->reactedPosts()->where('post_id', $post->id)->first();
        if ($existing) {

            $currentReaction = $existing->pivot->reaction;
            if ($validated['reaction'] === $currentReaction) {
                $user->reactedPosts()->detach($post->id);
                return response()->json(['message' => 'react removed'], 200);
            }

            $user->reactedPosts()->updateExistingPivot($post->id, [
                'reaction' => $validated['reaction'],
            ]);

            return response()->json(['message' => 'Reaction updated']);
        }
        $user->reactedPosts()->syncWithoutDetaching([
            $post->id => ['reaction' => $validated['reaction']]
        ]);

        return response()->json(['message' => 'Reacted']);
    }

    public function summary(Post $post)
    {
        $likes = $post->reactions()->wherePivot('reaction', '=', 'like')->count();
        $dislikes = $post->reactions()->wherePivot('reaction', '=', 'dislike')->count();

        return response()->json(['likes' => $likes, 'dislikes' => $dislikes]);
    }
}
