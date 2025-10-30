<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Traits\ApiResponseTrait;
class AuthController extends Controller
{
    use ApiResponseTrait;
    public function register(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => $validate['name'],
            'email' => $validate['email'],
            'password' => bcrypt($validate['password'])
        ]);

        $token = $user->createToken('api_token')->plainTextToken;
        return response()->json(['user' => new UserResource($user) , 'token' => $token , 'message' => 'login success']);
    }

    public function login(Request $request)
    {
        $validate = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required'
        ]);

        $user = User::where('email' , $validate['email'])->first();
        if (!$user || !Hash::check($validate['password'] , $user->password)){
            $this->errorResponse('the provided credentials are incorrect' , 'Credentials does not match' , 404);
        }

        $token = $user->createToken('api_token')->plainTextToken;
        return response()->json(['user' => new UserResource($user) , 'token' => $token , 'message' => 'login success']);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(message:  'Logged out successfully' , status: 200);
    }
}
