<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Registered successfully!',
            'data' => ['token' => $token]
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        // delete old tokens
        $user->tokens()->delete();
        
        $token = $user->createToken('api')->plainTextToken;
        
        return response()->json([
            'message' => 'Logged in successfully!',
            'data' => ['token' => $token]
        ]);
    }
    
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out successfully!']);
    }
    
    public function me(Request $request)
    {
        return response()->json(['data' => ['user' => $request->user()]]);
    }
}
