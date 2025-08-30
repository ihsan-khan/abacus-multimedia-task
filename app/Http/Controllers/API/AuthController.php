<?php

namespace App\Http\Controllers\API;

use App\Models\UserActivity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            $token = $user->createToken('YourAppName')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        $activity = UserActivity::where('user_id', $user->id)
            ->latest()
            ->first();

        if ($activity && !$activity->logout_time) {
            $activity->update(['logout_time' => now()]);
        }

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }
        return response()->json(['message' => 'Logged out successfully']);
    }
}
