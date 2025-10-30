<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
class ReactionController extends Controller
{
    use ApiResponseTrait;
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
                return $this->successResponse( message: 'react removed', status: 200);
            }

            $user->reactedPosts()->updateExistingPivot($post->id, [
                'reaction' => $validated['reaction'],
            ]);

            return $this->successResponse (message: 'Reaction updated', status: 200);
        }
        $user->reactedPosts()->syncWithoutDetaching([
            $post->id => ['reaction' => $validated['reaction']]
        ]);

        return $this->successResponse(message:  'Reacted' , status: 200);
    }

    public function summary(Post $post)
    {
        $likes = $post->reactions()->wherePivot('reaction', '=', 'like')->count();
        $dislikes = $post->reactions()->wherePivot('reaction', '=', 'dislike')->count();

        return response()->json(['likes' => $likes, 'dislikes' => $dislikes]);
    }
}
