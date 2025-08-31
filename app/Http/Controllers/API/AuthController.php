<?php

namespace App\Http\Controllers\API;

use App\Models\UserSession;
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
            $token = $user->createToken('Abacus Multimedia')->plainTextToken;

            $session = UserSession::create([
                'user_id' => auth()->id(),
                'login_at' => now(),
            ]);

            $user->update([
                'last_activity_at' => now()
            ]);

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

        $session = UserSession::where('user_id', auth()->id())
            ->whereNull('logout_at')
            ->latest()
            ->first();

        if ($session) {
            $session->update([
                'logout_at' => now(),
                'duration' => now()->diffInSeconds($session->login_at),
            ]);
        }

        if ($user->current_session_start) {
            $sessionDuration = now()->diffInSeconds($user->current_session_start);
            $user->total_online_seconds += $sessionDuration;

            // Reset session
            $user->current_session_start = null;
            $user->inactivity_threshold = null;
            $user->save();
            
        }

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }
        return response()->json(['message' => 'Logged out successfully']);
    }
}
