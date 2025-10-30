<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\ApiResponseTrait;
use function Symfony\Component\String\s;

class UserController extends Controller
{
    use ApiResponseTrait;
    public function show(Request $request )
    {
        return  $this->successResponse(
            new UserResource($request->user()->loadCount('posts')),
            'User received successfully',
            200
        );
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

        return $this->successResponse(new UserResource($user), 'User updated successfully', 200);
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        if($user->avatar){
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();
        return $this->successResponse(message: 'User deleted successfully' , status: 200);
    }

    public function destroyAvatar(Request $request){
        $user = $request->user();

        if(!$user->avatar){
            return $this->errorResponse(message: 'User doesn\'t have avatar' , status: 404);
        }
        Storage::disk('public')->delete($user->avatar);
        $user->update(['avatar' => null]);
        return $this->successResponse(
            new UserResource($user->fresh()),
            'User avatar deleted successfully',
            200
        );

    }
}
