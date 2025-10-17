<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
class AuthController extends Controller
{
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
        return response()->json(['user' => $user , 'token' => $token , 'massage' => 'login success']);
    }

    public function login(Request $request)
    {
        $validate = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required'
        ]);

        $user = User::where('email' , $validate['email'])->first();
        if (!$user || !Hash::check($validate['password'] , $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The information entered is incorrect.'],
            ]);
        }

        $token = $user->createToken('api_token')->plainTextToken;
        return response()->json(['user' => $user , 'token' => $token , 'massage' => 'login success']);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
