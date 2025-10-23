<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->loadCount('posts');
        return response()->json([
            'user' => $user,
            'message' => 'User received successfully'
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $validate = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:users,email,'.$user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if($user->avatar){
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $validate['avatar'] = $path;
        }

        $user->update($validate);

        return response()->json([
            'user' => $user,
            'message' => 'User updated successfully'
        ]);
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        if($user->avatar){
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function destroyAvatar(Request $request){
        $user = $request->user();

        if(!$user->avatar){
            return response()->json(['message' => 'User doesn\'t have avatar'],404);
        }
        Storage::disk('public')->delete($user->avatar);
        $user->update(['avatar' => null]);
        return response()->json([
            'user' => $user->fresh(),
            'message' => 'User avatar deleted successfully'
        ]);

    }
}
